<?php

namespace App\Repository\Economy;

use App\Models\Economy;
use App\Repository\Repository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EconomyRepository extends Repository
{
    public function execute($params): LengthAwarePaginator
    {
        $filter = $this->getFilterBuilder($params);

        foreach ($filter->getFilters() as $filters)
        {
            if (!$filters->getRow())
            {
                continue;
            }
            $filters->setField(DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY')"));
        }

        $field = collect($filter->getFields())->merge($this->getRowField())->all();

        $query = $this->model->newQuery()
            ->select($field)
            ->join(
                "dados_cadastrais",
                "dados_cadastrais.cod_smart_unidade",
                "=",
                "economia.cod_smart_unidade",
            );

            $res = $filter->applyFilter($query);

        return $res->paginate();

    }

    public function getRowField(): array
    {
        return [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY') as mes"),
            DB::raw("TRIM(TO_CHAR(economia.custo_cativo, '99999999.99')) as custo_cativo"),
            DB::raw("TRIM(TO_CHAR(economia.custo_livre, '99999999.99')) as custo_livre"),
            DB::raw("COALESCE(economia.economia_mensal / NULLIF(economia.custo_livre, 0), 0) as custo")
        ];
    }

    protected function model(): string
    {
        return Economy::class;
    }
}
