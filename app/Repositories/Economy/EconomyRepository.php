<?php

declare(strict_types=1);

namespace App\Repositories\Economy;

use App\Models\Economy;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EconomyRepository extends AbstractRepository implements EconomyContractInterface
{

    public function __construct(Economy $economy)
    {
        parent::__construct($economy);
    }

    public function execute($params, $field): Builder
    {

       $query = $this->model
           ->select(
               $field
           )
            ->join(
                "dados_cadastrais",
                "dados_cadastrais.cod_smart_unidade",
                "=",
                "economia.cod_smart_unidade",
            );

        if (!empty($params)) {
            $query = static::getFilterBuilder($params)->applyFilter($query);
        }
        return $query;
    }

    public function getGrossEconomy($params): Collection|array
    {
        $field = [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY') as mes"),
            DB::raw("SUM(economia.economia_acumulada) as economia_acumulada"),
            DB::raw("(SUM(economia.economia_mensal)/SUM(economia.custo_livre)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        return $this->execute($params, $field)
             ->where(DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                 ">=",
                 DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '1' year"))
            ->groupBy(['mes', 'dad_estimado'])
            ->get();
    }

    public function getAccumulatedEconomy($params)
    {
        // TODO: Implement getAccumulatedEconomy() method.
    }

    public function getCostEstimatesEconomy($params)
    {
        // TODO: Implement getCostEstimatesEconomy() method.
    }

    public function getCostMWhEconomy($params)
    {
        $field = [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY') as mes"),
            DB::raw("SUM(economia.custo_unit) as custo_unit"),
            "economia.dad_estimado"
        ];

        return $this->execute($params, $field)
            ->whereBetween(DB::raw("TO_DATE(economia.mes, 'YYMM')"),
            [
                DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '1' year"),
                DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-12-31'), 'YYYY-MM-DD') ")
            ]
            )
            ->groupBy(['mes', 'dad_estimado'])
            ->get();

    }


    protected function where($query)
    {
        return $query->where(
            DB::raw(

            )
        );
    }


    protected function getRowField(): array
    {
        return [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY') as mes"),
            DB::raw("TRIM(TO_CHAR(economia.custo_cativo, '99999999.99')) as custo_cativo"),
            DB::raw("TRIM(TO_CHAR(economia.custo_livre, '99999999.99')) as custo_livre"),
            DB::raw("COALESCE(economia.economia_mensal / NULLIF(economia.custo_livre, 0), 0) as custo")
        ];
    }
}
