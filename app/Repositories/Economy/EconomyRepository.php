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
            DB::raw("MAX(economia.mes) as mes"),
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY') as ano"),
            DB::raw("SUM(economia.economia_mensal)/1000 as economia_acumulada_a"),
            DB::raw("SUM(economia.economia_acumulada)/1000 as economia_acumulada"),
            DB::raw("(SUM(economia.economia_mensal)/SUM(economia.custo_cativo)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        $maxMonths = $this->model->newQuery();

        if (!empty($params)) {
            $maxMonths = static::getFilterBuilder($params)->applyFilter($maxMonths);
        }

        $maxMonths = $maxMonths
            ->select([
                DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY') as ano"),
                'economia.cod_smart_unidade',
                'economia.dad_estimado',
                DB::raw("MAX(economia.mes) as mes"),
            ])
            ->where(
                DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                ">=",
                DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-12-01'), 'YYYY-MM-DD') - interval '24' month")
            )
            ->groupBy([
                DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY')"),
                'economia.cod_smart_unidade',
                'economia.dad_estimado',
            ]);

        return $this->execute($params, $field)
            ->joinSub($maxMonths, 'max_economia', function ($join) {
                $join->on('economia.mes', '=', 'max_economia.mes')
                    ->on(DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY')"), '=', DB::raw('max_economia.ano'))
                    ->on('economia.cod_smart_unidade', '=', 'max_economia.cod_smart_unidade')
                    ->on('economia.dad_estimado', '=', 'max_economia.dad_estimado');
            })
            ->groupBy([
                DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY')"),
                'economia.dad_estimado',
            ])
            ->havingRaw("sum(custo_livre) > 0")
            ->orderBy(DB::raw("ano, economia.dad_estimado"))
            ->get();
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
            DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-MM-DD'), 'YYYY-MM-DD') + interval '1' year"))
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
                    DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-MM-DD'), 'YYYY-MM-DD') - interval '13' month"),
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
