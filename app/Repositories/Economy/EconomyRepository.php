<?php

namespace App\Repositories\Economy;

use App\Models\Economy;
use App\Repositories\AbstractRepository;
use App\Support\FilterBuilder\FilterQueryBuilder;
use Illuminate\Support\Facades\DB;

class EconomyRepository extends AbstractRepository implements EconomyContractInterface
{

    public function __construct(Economy $economy)
    {
        parent::__construct($economy);
    }

    public function execute($params)
    {

       $test = FilterQueryBuilder::for($params);
        dd($test);

       $query = $this->model
           ->select(
               $this->getRowField()
           )
            ->join(
                "dados_cadastrais",
                "dados_cadastrais.cod_smart_unidade",
                "=",
                "economia.cod_smart_unidade",
            );

        dd( $query->limit(5)->get());

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
