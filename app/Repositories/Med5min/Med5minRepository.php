<?php

declare(strict_types=1);

namespace App\Repositories\Med5min;

use App\Models\Med5min;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class Med5minRepository extends AbstractRepository implements Med5minContractInterface
{

    public function __construct(Med5min $med5min)
    {
        parent::__construct($med5min);
    }

    private function execute($fields, $params): Builder
    {
        $query = $this->model->select($fields);

        if (!empty($params)) {
            $query = static::getFilterBuilder($params)->applyFilter($query);
        }

        return $query;
    }

    public function discretized5min($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("MOD(med_5min.minuto,60) AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("UM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];


        return $this->execute($fields, $params);

    }

    public function discretized15min($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("MOD(med_5min.minuto,60) AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("UM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        return $this->execute($fields, $params);
    }

    public function discretizedOneHour($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("MOD(med_5min.minuto,60) AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("UM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];;

        return $this->execute($fields, $params);
    }

    public function discretizedOneDay($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("MOD(med_5min.minuto,60) AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("UM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        return $this->execute($fields, $params);
    }

}
