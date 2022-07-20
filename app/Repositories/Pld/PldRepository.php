<?php

declare(strict_types=1);

namespace App\Repositories\Pld;

use App\Models\Pld;
use App\Repositories\AbstractRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PldRepository extends AbstractRepository implements PldContractInterface
{
    public function __construct(Pld $pld)
    {
        parent::__construct($pld);
    }

    /**
     * @throws BindingResolutionException
     */
    private function execute($fields, $params = []): Builder
    {
        $query = $this->model->select($fields);

        if (!empty($params)) {
            $query = static::getFilterBuilder($params)->applyFilter($query);
        }
        return $query;
    }


    /**
     * Geral por Região
     */
    public function getOverviewByRegion(): array|Collection
    {
        $fields = [
            'pld.submercado as submarket',
            'pld.mes_ref as year_month',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
            DB::raw("AVG(pld.valor) as value")
        ];


        return $this->execute($fields)
            ->where(DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY')"), '=', Carbon::now()->format('m/Y'))
            ->groupBy(['submarket', 'year_month', 'year_month_formatted'])
            ->get();
    }

    /** Tabela de Consumo */
    public function getListConsumption($params): Collection|array
    {
        $fields = [
            'pld.mes_ref as year_month',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
            DB::raw("pld_norte.value as norte"),
            DB::raw("pld_sul.value as sul"),
            DB::raw("pld_nordeste.value as nordeste"),
            DB::raw("pld_sudeste.value as sudeste"),
        ];

        $res_max = [];
        $res_min = [];
        $desv_pad = [];
        $sql = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('AVG(valor) as value'),
            ])
            ->where('pld.submercado', '=', 'NORTE')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql, 'norte');
        $res_max["norte_max"] = static::max($query);
        $res_min["norte_min"] = static::min($query);
        $desv_pad["norte_desv_pad"] = static::standardDeviation($sql);

        $sql2 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('AVG(valor) as value')
            ])
            ->where('pld.submercado', '=', 'SUL')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql2, 'sul');
        $res_max["sul_max"] = static::max($query);
        $res_min["sul_min"] = static::min($query);
        $desv_pad["sul_desv_pad"] = static::standardDeviation($sql2);

        $sql3 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('AVG(valor) as value')
            ])
            ->where('pld.submercado', '=', 'NORDESTE')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql3, 'nordeste');
        $res_max["nordeste_max"] = static::max($query);
        $res_min["nordeste_min"] = static::min($query);
        $desv_pad["nordeste_desv_pad"] = static::standardDeviation($sql3);

        $sql4 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('AVG(valor) as value')
            ])
            ->where('pld.submercado', '=', 'SUDESTE')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql4, 'sudeste');
        $res_max["sudeste_max"] = static::max($query);
        $res_min["sudeste_min"] = static::min($query);
        $desv_pad["sudeste_desv_pad"] = static::standardDeviation($sql3);

        $data = $this->model->select($fields)->joinSub($sql, 'pld_norte', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_norte.year_month');
        })->joinSub($sql2, 'pld_sul', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_sul.year_month');
        })->joinSub($sql3, 'pld_nordeste', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_nordeste.year_month');
        })->joinSub($sql4, 'pld_sudeste', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_sudeste.year_month');
        })
            ->whereRaw("TO_DATE(pld.mes_ref, 'YYMM') >= TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - INTERVAL '1' year")
            ->orderBy('year_month', 'DESC')
            ->distinct()
            ->get();

        return [
            'data' => $data,
            'result' => [
                $res_max,
                $res_min,
                $desv_pad
            ]
        ];
    }

    /**
     * @throws BindingResolutionException
     *  PLD, Valores Diários
     */
    public function getConsumptionByDaily($params, $field = "mes_ref"): Collection|array
    {
        $fields = [
            DB::raw("to_char(('1899-12-30'::date + ('1 day'::interval day * (pld.dia_num)::double precision)), 'DD'::text) AS day_formatted"),
            DB::raw("('1899-12-30'::date + ('1 day'::interval day * (pld.dia_num)::double precision)) as day_calc"),
            'pld.submercado as submarket',
            DB::raw("AVG(pld.valor) as value"),
            DB::raw("pld.mes_ref as year_month"),
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'::text)::timestamp with time zone, 'MM/YYYY'::text) as year_month_formatted")
        ];

        $fields2 = ['day_formatted',
            'day_calc',
            'submarket',
            'year_month',
            'year_month_formatted',
            'value',
            DB::raw('avg(value) over(ORDER BY day_formatted) as mMovel')
        ];

        $query = $this->execute($fields)
            ->groupBy(
                DB::raw("(to_char(('1899-12-30'::date + ('1 day'::interval day * (pld.dia_num)::double precision)), 'DD'::text))"),
                DB::raw("('1899-12-30'::date + ('1 day'::interval day * (pld.dia_num)::double precision))"),
                "pld.submercado",
                "pld.mes_ref",
                DB::raw("(to_char((to_date(pld.mes_ref, 'YYMM'::text))::timestamp with time zone, 'MM/YYYY'::text))")
            )
            ->orderByRaw("('1899-12-30'::date + ('1 day'::interval day * (pld.dia_num)::double precision))");

        return $this->execute($fields2, $params)
            ->from($query, 'plds')
            ->get();
    }

    public function getConsumptionBySchedule($params, $field = "dia_num"): Collection|array
    {
        $fields = [
            DB::raw("TO_CHAR((date('1899-12-31') + interval '1' day * pld.dia_num), 'DD') as day_formatted"),
            'hora as hour',
            DB::raw("(date('1899-12-31') + interval '1' day * pld.dia_num) as day_calc"),
            'pld.submercado as submarket',
            'pld.valor as value',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month"),
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
        ];

        $i = 0;
        foreach ($params['filters'] as $param) {
            if ($param['field'] === $field) {
                $params['filters'][$i]['field'] = "(date('1899-12-30') + interval '1' day * pld.{$param['field']})";
            }
            $i++;
        }

        return $this->execute($fields, $params)
            ->whereRaw("TO_DATE(pld.mes_ref, 'YYMM') >= TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - INTERVAL '1' year")
            ->orderBy('hour',  'asc')
            ->get();
    }

    protected static function max($query){
        return $query->max(DB::raw("value"));
    }

    protected static function min($query){
        return $query->min(DB::raw("value"));
    }

    protected static function standardDeviation($query): float|bool
    {
        $array = $query->addSelect([
            DB::raw("AVG(valor)as desv_pad")
        ])->get()->toArray();

        return stats_standard_deviation(collect($array)->pluck('desv_pad')->all());
    }

}