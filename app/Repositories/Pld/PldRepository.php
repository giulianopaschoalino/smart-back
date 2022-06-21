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
     * @throws BindingResolutionException
     */
    public function getOverviewByRegion(): Collection|array
    {
        $fields = [
            'pld.submercado as submarket',
            'pld.mes_ref as year_month',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
            DB::raw("SUM(pld.valor) as value")
        ];

        // Carbon::now()->format('m/Y')
        return $this->execute($fields)
            ->where(DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY')"), '=', '04/2022')
            ->groupBy(['submarket', 'year_month', 'year_month_formatted'])
            ->get();
    }

    public function getListConsumption($params): Collection|array
    {
        $fields = [
            'pld.mes_ref as year_month',
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
            DB::raw('pld_norte.value as norte'),
            DB::raw('pld_sul.value as sul'),
            DB::raw('pld_nordeste.value as nordeste'),
            DB::raw('pld_sudeste.value as sudeste'),
        ];

        $data = [];
        $sql = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value'),
            ])
            ->where('pld.submercado', '=', 'NORTE')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql, 'norte');
        $max_norte = $query->max('value');
        $min_norte = $query->min('value');
        $desvio_padrao = static::standardDeviation($sql->get()->toArray());

        $data[] = ['norte' =>
            [
                'max' => $max_norte,
                'min' => $min_norte,
                'desv_pad' => $desvio_padrao
            ]
        ];

        dd($data);

        $sql2 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value')
            ])
            ->where('pld.submercado', '=', 'SUL')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql2, 'sul');
        $max_sul = $query->max('value');
        $min_sul = $query->min('value');
        $desvio_padrao = static::standardDeviation($sql2->get()->toArray());

        $data[] = ['sul' =>
            [
                'max' => $max_sul,
                'min' => $min_sul,
                'desv_pad' => $desvio_padrao
            ]
        ];

        $sql3 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value')
            ])
            ->where('pld.submercado', '=', 'NORDESTE')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql3, 'nordeste');
        $max_nordeste = $query->max('value');
        $min_nordeste = $query->min('value');
        $desvio_padrao = static::standardDeviation($sql3->get()->toArray());


        $data[] = ['nordeste' =>
            [
                'max' => $max_nordeste,
                'min' => $min_nordeste,
                'desv_pad' => $desvio_padrao
            ]
        ];

        $sql4 = DB::table('pld')
            ->select([
                'submercado as submarket',
                'mes_ref as year_month',
                DB::raw('SUM(valor) as value')
            ])
            ->where('pld.submercado', '=', 'SUDESTE')
            ->groupBy('submarket', 'year_month');

        $query = DB::table('pld')->fromSub($sql4, 'sudeste');
        $max_sudeste = $query->max('value');
        $min_sudeste = $query->min('value');
        $desvio_padrao = static::standardDeviation($sql4->get()->toArray());

        $data[] = ['nordeste' =>
            [
                'max' => $max_sudeste,
                'min' => $min_sudeste,
                'desv_pad' => $desvio_padrao
            ]
        ];

        $result = $this->model->select($fields)->joinSub($sql, 'pld_norte', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_norte.year_month');
        })->joinSub($sql2, 'pld_sul', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_sul.year_month');
        })->joinSub($sql3, 'pld_nordeste', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_nordeste.year_month');
        })->joinSub($sql4, 'pld_sudeste', function ($join) {
            $join->on('pld.mes_ref', '=', 'pld_sudeste.year_month');
        })->distinct()->get();




    }

    /**
     * @throws BindingResolutionException
     */
    public function getConsumptionByDaily($params, $field = "mes_ref"): Collection|array
    {
        $fields = [
            DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * pld.dia_num), 'DD') as day_formatted"),
            DB::raw("(date('1899-12-30') + interval '1' day * pld.dia_num) as day_calc"),
            'pld.submercado as submarket',
            DB::raw("SUM(pld.valor) as value"),
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month"),
            DB::raw("TO_CHAR(TO_DATE(pld.mes_ref, 'YYMM'), 'MM/YYYY') as year_month_formatted"),
        ];

        $i = 0;
        foreach ($params['filters'] as $param) {
            if ($param['field'] === $field) {
                $params['filters'][$i]['field'] = "TO_CHAR(TO_DATE(pld.{$param['field']}, 'YYMM'), 'MM/YYYY')";
            }
            $i++;
        }

        return $this->execute($fields, $params)
            ->groupBy('day_formatted', 'day_calc', 'submarket', 'year_month', 'year_month_formatted')
            ->get();

    }

    public function getConsumptionBySchedule($params, $field = "dia_num"): Collection|array
    {
        $fields = [
            DB::raw("TO_CHAR((date('1899-12-30') + interval '1' day * pld.dia_num), 'DD') as day_formatted"),
            'hora as hour',
            DB::raw("(date('1899-12-30') + interval '1' day * pld.dia_num) as day_calc"),
            'pld.submercado as submarket',
            DB::raw("SUM(pld.valor) as value"),
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
            ->groupBy('day_formatted', 'hour', 'day_calc', 'submarket', 'year_month', 'year_month_formatted')
            ->get();
    }

    protected static function standardDeviation($array): float|bool
    {
       return stats_standard_deviation(collect($array)->pluck('value')->all());
    }

    protected static function responsePld($query, $name){

    }

}