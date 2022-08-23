<?php

declare(strict_types=1);

namespace App\Repositories\Med5min;

use App\Helpers\Helpers;
use App\Models\Med5min;
use App\Repositories\AbstractRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


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

    public function getDiscretization($params, $path): Collection|array
    {
        if (empty($params['type'])) {
            return abort(404, 'Error! The type field needs to be filled in.');
        }

        $typeField = collect($path)->map(function ($item) {
            $value = Str::of($item)->explode('/')->offsetGet(3);
            return $value === "powerFactor" ? true : ($value === "demand" ? false : null);
        })->first();

        $type = $params['type'];

        $params = static::filterRow($params);

        return match ($type) {
            '5_min' => $this->getDiscretized5min($params, $typeField),
            '15_min' => $this->getDiscretized15min($params, $typeField),
            '1_hora' => $this->getDiscretizedOneHour($params, $typeField),
            '1_dia' => $this->getDiscretizedOneDay($params, $typeField),
            '1_mes' => $this->getDiscretizedOneMonth($params, $typeField)
        };
    }

    public function getDiscretized5min($params, bool $typeField = null)
    {

        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("((med_5min.minuto-5)/60) AS hora"),
                DB::raw("MOD((med_5min.minuto-5),60) AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        if (!is_null($typeField)) {
            $fields = $this->typeField($fields, $typeField);
        }

        $groupBy = $this->groupField($typeField);

        $result = $this->execute($fields, $params)
            ->groupBy($groupBy)
            ->distinct()
            ->get();

        return Helpers::formatOfFooter($result);
    }

    public function getDiscretized15min($params, $typeField = null)
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("((med_5min.minuto-5)/60) AS hora"),
                DB::raw("((MOD((med_5min.minuto-5),60)/15)+1)*15 AS minut"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        if (!is_null($typeField)) {
            $fields = $this->typeField($fields, $typeField);
        }

        $groupBy = $this->groupField($typeField);

        $result = $this->execute($fields, $params)
            ->groupBy($groupBy)
            ->distinct()
            ->get();

        return Helpers::formatOfFooter($result);
    }

    public function getDiscretizedOneHour($params, $typeField = null, string $type = '1_hora'): Collection|array
    {
        //retirado -5 pelo motivo que o minuto 1440 na verdade é o intervalo de consumo entre 23:55:01 até 00:00:00. Por 00:00:00 cair no dia seguinte, estava dando problema no gráfico.
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("((med_5min.minuto-5)/60) AS hora"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        if (!is_null($typeField)) {
            $fields = $this->typeField($fields, $typeField);
        }

        $groupBy = $this->groupField($typeField, $type);

        $result = $this->execute($fields, $params)
            ->groupBy($groupBy)
            ->distinct()
            ->get();

        return Helpers::formatOfFooter($result);
    }

    public function getDiscretizedOneDay($params, $typeField = null, string $type = '1_dia'): Collection|array
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("SUM(med_5min.ativa_consumo) AS consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) AS reativa")
            ];

        if (!is_null($typeField)) {
            $fields = $this->typeField($fields, $typeField);
        }

        $groupBy = $this->groupField($typeField, $type);

        return $this->execute($fields, $params)
            ->groupBy($groupBy)
            ->distinct()
            ->get();
    }

    public function getDiscretizedOneMonth($params, $typeField = null, string $type = '1_mes'): Collection|array
    {
        $fields =
            [
                'med_5min.ponto',
                'med_5min.dia_num',
                DB::raw("(
                    med_5min.dia_num::INTEGER - extract(day from (
                        (date('1899-12-30') + interval '1' day * med_5min.dia_num)
                        -
                        to_date(
                            concat(
                                extract( YEAR from date '1899-12-30' + cast (med_5min.dia_num as integer)), 
                                '/',
                                extract( month from date '1899-12-30' + cast (med_5min.dia_num as integer))
                            ), 
                            'YYYY/MM'
                        )
                    ))
	            ) as dia_data"),
                DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * med_5min.dia_num), 'DD/MM/YYYY') as day_formatted"),
                DB::raw("SUM(med_5min.ativa_consumo) As consumo"),
                DB::raw("SUM(med_5min.reativa_consumo+med_5min.reativa_geracao) As reativa")
            ];

        if (!is_null($typeField)) {
            $fields = $this->typeField($fields, $typeField);
        }

        $groupBy = $this->groupField($typeField, $type);

        return $this->execute($fields, $params)
            ->groupBy($groupBy)
            ->orderBy(DB::raw("med_5min.dia_num, med_5min.ponto"))
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

    private function typeField(array $fields, bool $typeField): array
    {

        return collect($fields)->when($typeField, function ($collection, $value) {

            $field =
                [
//                    DB::raw("(SUM(med_5min.ativa_consumo)/SQRT(((SUM(med_5min.ativa_consumo)^2) + (SUM(med_5min.reativa_consumo+med_5min.reativa_geracao)^2)))) as FP"),
                    DB::raw("
                    (
                        SUM(med_5min.ativa_consumo)
                        /
                        SQRT(
                            SUM(med_5min.ativa_consumo)^2
                            +
                            SUM(med_5min.reativa_consumo+med_5min.reativa_geracao)^2
                        )
                    ) as FP"),
                    DB::raw("0.92 as F_ref")
                ];

            return $collection->merge($field);

        }, function ($collection, $value) {

            $field =
                [
		            DB::raw("(CASE WHEN (((med_5min.minuto-5)/60) >= 18 AND ((med_5min.minuto-5)/60) < 21 AND extract( dow from date '1899-12-30' + cast (med_5min.dia_num as integer)) BETWEEN 1 AND 5) THEN dados_cadastrais.demanda_p ELSE dados_cadastrais.demanda_fp  END)*1.05 as dem_tolerancia"),
                    DB::raw("SUM(med_5min.ativa_consumo) AS dem_reg"),
                    DB::raw("(CASE WHEN (((med_5min.minuto-5)/60) >= 18 AND ((med_5min.minuto-5)/60) <= 21 AND extract( dow from date '1899-12-30' + cast (med_5min.dia_num as integer)) BETWEEN 1 AND 5) THEN dados_cadastrais.demanda_p ELSE dados_cadastrais.demanda_fp  END) as dem_cont")
                ];

            return $collection->merge($field);

        })->all();

    }

    public function groupField($typeField, $type = null): array
    {
        $fields = ["med_5min.ponto", "med_5min.dia_num", "day_formatted", 'hora', 'minut'];

        if ($type === '1_hora') {
            array_splice($fields, 4);
        }

        if ($type === '1_dia' || $type === '1_mes') {
            array_splice($fields, 3);
        }

        if ($typeField === false) {
            $item = ['dem_cont', 'dem_tolerancia'];
            return collect($fields)->merge($item)->all();
        }

        return $fields;
    }


}
