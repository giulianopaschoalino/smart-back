<?php

declare(strict_types=1);

namespace App\Repositories\Economy;

use App\Helpers\Helpers;
use App\Models\Economy;
use App\Repositories\AbstractRepository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EconomyRepository extends AbstractRepository implements EconomyContractInterface
{

    public function __construct(Economy $economy)
    {
        parent::__construct($economy);
    }

    public function execute($params, $field): Builder
    {

        $query = $this->model->select($field);

        if (!empty($params)) {
            $query = static::getFilterBuilder($params)->applyFilter($query);
        }
        return $query;
    }

    /* Economia bruta anual */
    public function getGrossAnnualEconomy($params): Collection|array
    {
        $field = [
            "economia.mes",
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY') as ano"),
            DB::raw("SUM(economia.economia_mensal)/1000 as economia_acumulada_a"),
            DB::raw("SUM(economia.economia_acumulada)/1000 as economia_acumulada"),
            DB::raw("(SUM(economia.economia_mensal)/SUM(economia.custo_cativo)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        $result = $this->execute($params, $field)
            ->where(
                DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                ">=",
                DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-12-01'), 'YYYY-MM-DD') - interval '2' year"))
            ->where(function ($query) {
                $query->where(DB::raw("extract(month from TO_DATE(economia.mes, 'YYMM'))"), '=', 12)
                    ->orWhere(function ($query) {
                        $query->where(DB::raw("extract(year from TO_DATE(economia.mes, 'YYMM'))"), '=', DB::raw('extract(year from NOW())'))
                            ->whereIn(DB::raw("extract(month from TO_DATE(economia.mes, 'YYMM'))"),
                                DB::table('economia')
                                    ->selectRaw(
                                        "max(extract(month from TO_DATE(mes, 'YYMM')))"
                                    )
                                    ->where('dad_estimado', '=', false)
                                    ->where(DB::raw("extract(year from TO_DATE(mes, 'YYMM'))"), '=', DB::raw('extract(year from NOW())'))
                                    ->whereIn(
                                        'economia.cod_smart_unidade',
                                        DB::table('dados_cadastrais')
                                            ->select('cod_smart_unidade')
                                            ->where('dados_cadastrais.codigo_scde', '!=', '0P')
                                            ->where('dados_cadastrais.cod_smart_cliente', '=', Auth::user()->client_id)
                                    )
                            );
                    });
            })
            ->groupBy(['mes', 'ano', 'dad_estimado'])
            ->havingRaw("sum(custo_livre) > 0")
            ->orderBy(DB::raw("mes, ano, dad_estimado"))
            ->get();

        // Find the last fully consolidated year (December with dad_estimado = false)
        $lastConsolidatedYear = null;
        foreach ($result as $item) {
            if ($item->dad_estimado === false || $item->dad_estimado === 0) {
                $month = (int)substr($item->mes, -2);
                if ($month === 12) {
                    $year = (int)$item->ano;
                    if ($lastConsolidatedYear === null || $year > $lastConsolidatedYear) {
                        $lastConsolidatedYear = $year;
                    }
                }
            }
        }

        // If no consolidated year found, use current year - 1
        if ($lastConsolidatedYear === null) {
            $lastConsolidatedYear = (int)date('Y') - 1;
        }

        // Create result array with sequential years (last consolidated + 6 more years)
        $sequentialResult = [];
        for ($year = $lastConsolidatedYear; $year <= $lastConsolidatedYear + 6; $year++) {
            $yearData = $result->filter(function ($item) use ($year) {
                return (int)$item->ano === $year;
            });

            if ($yearData->count() > 0) {
                foreach ($yearData as $item) {
                    $sequentialResult[] = $item;
                }
            } else {
                // Fill missing year with estimated data from closest available year
                $closestData = $result->filter(function ($item) {
                    return (int)$item->ano > 0;
                })->sortBy('ano')->last();

                if ($closestData) {
                    $newItem = clone $closestData;
                    $newItem->ano = (string)$year;
                    $newItem->dad_estimado = true;
                    $sequentialResult[] = $newItem;
                }
            }
        }

        return collect($sequentialResult);
    }

    /* Economia bruta mensal */
    public function getGrossMonthlyEconomy($params)
    {
        $field = [
            "economia.mes",
            DB::raw("SUM(economia.economia_acumulada)/1000 as economia_acumulada"),
            DB::raw("(SUM(economia.economia_mensal)/SUM(economia.custo_cativo)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        $result = $this->execute($params, $field)
            ->where('dados_cadastrais.codigo_scde', '!=', '0P')
            ->where(DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                ">=",
                DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-01-01'), 'YYYY-MM-DD') - interval '2' year"))
            ->where(DB::raw("TO_DATE(economia.mes, 'YYMM')"),
            "<=",
            DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-MM-DD'), 'YYYY-MM-DD')"))
            ->groupBy(['mes', 'dad_estimado'])
            ->orderBy(DB::raw("mes, dad_estimado"))
            ->havingRaw("sum(custo_livre) > 0")
            ->get();

        return Helpers::orderByDate($result);

    }

    /*  cativo x livre mensal*/
    public function getCaptiveMonthlyEconomy($params): Collection|array
    {

        $field = [
            "economia.mes",
            DB::raw("SUM(economia.custo_cativo)/1000 as custo_cativo"),
            DB::raw("SUM(economia.custo_livre)/1000 as custo_livre"),
            DB::raw("SUM(economia.economia_mensal)/1000 as economia_mensal"),
            DB::raw("(SUM(economia_mensal)/SUM(custo_livre)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        $result = $this->execute($params, $field)
            ->where('dados_cadastrais.codigo_scde', '!=', '0P')
            ->whereBetween(
                DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                [
                    DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '2' year"),
                    DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-MM-DD'), 'YYYY-MM-DD') ")
                ])
            // ->whereRaw("TO_DATE(economia.mes, 'YYMM') >= TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - INTERVAL '0' year")
            ->groupBy(['mes', 'dad_estimado'])
            ->havingRaw("sum(custo_livre) > 0")
            ->orderBy(DB::raw("mes, dad_estimado"))
            ->get();

        return Helpers::orderByDate($result);
    }

    /* Indicador de custo R$/MWh */
    public function getCostMWhEconomy($params)
    {
        $field = [
            DB::raw("TO_DATE(economia.mes, 'YYMM') as mes"),
            DB::raw("SUM(economia.custo_unit)/COUNT(economia.custo_unit) as custo_unit"),
            "economia.dad_estimado"
        ];

        $value = $this->execute($params, $field)
            ->whereBetween(
                DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                [
                    DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '2' year"),
                    DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-12-31'), 'YYYY-MM-DD') ")
                ])
            ->groupBy(['mes', 'dad_estimado'])
            ->orderBy(DB::raw("mes, dad_estimado"))
            ->get();

       return Helpers::checkDate($value);
    }

}
