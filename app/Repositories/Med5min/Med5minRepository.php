<?php

declare(strict_types=1);

namespace App\Repositories\Med5min;

use App\Models\Med5min;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
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

    public function getPowerFactor($params): Collection|array
    {
        $fields =
            [
                "med_5min.ponto",
                "med_5min.dia_num",
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa"),
                DB::raw("(SUM(med_5min.ativa_consumo) / NULLIF(( (SUM(med_5min.ativa_consumo)^2) + (SUM(med_5min.reativa_consumo+med_5min.reativa_geracao)^2) ), 0))*100 as FP"),
                DB::raw("0.92 as F_ref")
            ];

        $params = static::filterRow($params);

        return $this->execute($fields, $params)
            ->groupBy(["med_5min.minuto", "med_5min.ponto", "med_5min.dia_num"])
            ->distinct()
            ->get();


    }

    public function getDemand($params): Collection|array
    {
        $fields =
            [
                "med_5min.ponto",
                "med_5min.dia_num",
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("SUM(med_5min.ativa_consumo) AS dem_reg"),
                DB::raw("(CASE WHEN ((med_5min.minuto/60) >= 18 AND (med_5min.minuto/60) <= 21) THEN dados_cadastrais.demanda_p ELSE dados_cadastrais.demanda_fp  END) as dem_cont")
        ];

        $params = static::filterRow($params);

        return $this->execute($fields, $params)
            ->join(
                "dados_cadastrais",
                "dados_cadastrais.codigo_scde",
                "=",
                "med_5min.ponto"
            )
            ->groupBy(["med_5min.ponto", "med_5min.dia_num", "day_formatted", 'hora', 'dem_cont'])
            ->distinct()
            ->get();
    }


    public function getDiscretization($params)
    {
        if (empty( $params['type'])){
           return abort(404, 'Error! The type field needs to be filled in.');
        }

        $type = $params['type'];

        $params = static::filterRow($params);

        return match ($type) {
                '5_min'  => $this->getDiscretized5min($params),
                '15_min' => $this->getDiscretized15min($params),
                '1_hora' => $this->getDiscretizedOneHour($params),
                '1_dia'  => $this->getDiscretizedOneDay($params)
        };
    }

    public function getDiscretized5min($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("MOD(med_5min.minuto,60) AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        return $this->execute($fields, $params)
            ->groupBy(["med_5min.ponto", "med_5min.dia_num", "day_formatted", 'hora', 'minut'])
            ->distinct()
            ->get();

    }

    public function getDiscretized15min($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("((MOD(med_5min.minuto,60)/15)+1)*15 AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        return $this->execute($fields, $params)
            ->groupBy(["med_5min.ponto", "med_5min.dia_num", "day_formatted", 'hora', 'minut'])
            ->distinct()
            ->get();
    }

    public function getDiscretizedOneHour($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("(med_5min.minuto/60) AS hora"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];;

        return $this->execute($fields, $params)
            ->groupBy(["med_5min.ponto", "med_5min.dia_num", "day_formatted", 'hora'])
            ->distinct()
            ->get();
    }

    public function getDiscretizedOneDay($params)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        return $this->execute($fields, $params)
            ->groupBy(["med_5min.ponto", "med_5min.dia_num", "day_formatted"])
            ->distinct()
            ->get();
    }

    public static function filterRow($params, $field = 'dia_num'): array
    {
        $arr['filters'] = collect($params['filters'])
            ->map(function ($value) use ($field) {
                if ($value['field'] === $field) {
                    Arr::set($value, "field", "(date('1899-12-30') + interval '1' DAY * med_5min.{$value['field']})");
                    $value['row'] = true;
                }
                return $value;
            })->all();
        return $arr;
    }

}
