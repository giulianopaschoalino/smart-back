<?php

declare(strict_types=1);

namespace App\Repositories\Economy;

use App\Models\Economy;
use App\Repositories\AbstractRepository;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY') as ano"),
            DB::raw("SUM(economia.economia_acumulada)/1000 as economia_acumulada"),
            DB::raw("(SUM(economia.economia_mensal)/SUM(economia.custo_livre)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        DB::enableQueryLog();
         $this->execute($params, $field)
            ->where(DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                ">=",
                DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '1' year"))
            ->groupBy(['ano', 'dad_estimado'])
            ->orderBy(DB::raw("ano, dad_estimado"))
            ->get();
        dd(DB::getQueryLog());
    }

    /* Economia bruta mensal */
    public function getGrossMonthlyEconomy($params)
    {
        $field = [
            DB::raw("TO_DATE(economia.mes, 'YYMM') as mes"),
            DB::raw("SUM(economia.economia_acumulada)/1000 as economia_acumulada"),
            DB::raw("(SUM(economia.economia_mensal)/SUM(economia.custo_livre)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        $result = $this->execute($params, $field)
            ->where(DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                ">=",
                DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '1' year"))
            ->groupBy(['mes', 'dad_estimado'])
            ->orderBy(DB::raw("mes, dad_estimado"))
            ->get();

         return collect(static::checkDate($result))->transform(fn($value) => Arr::set($value, 'mes', date_format(date_create($value['mes']), "M/Y")))->all();

    }

    /*  cativo x livre mensal*/
    public function getCaptiveMonthlyEconomy($params)
    {
        $field = [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY') as mes"),
            DB::raw("SUM(economia.custo_cativo)/1000 as custo_cativo"),
            DB::raw("SUM(economia.custo_livre)/1000  as custo_livre"),
            DB::raw("SUM(economia.economia_mensal)/1000 as economia_mensal"),
            DB::raw("(SUM(economia_mensal)/SUM(custo_livre)) as econ_percentual"),
            "economia.dad_estimado"
        ];

        return $this->execute($params, $field)
            ->whereBetween(
                DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                [
                    DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '1' year"),
                    DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-12-31'), 'YYYY-MM-DD') ")
                ])
            ->groupBy(['mes', 'dad_estimado'])
            ->orderBy(DB::raw("mes, dad_estimado"))
            ->get();
    }

    /* Indicador de custo R$/MWh */
    public function getCostMWhEconomy($params)
    {
        $field = [
            DB::raw("TO_DATE(economia.mes, 'YYMM') as mes"),
            DB::raw("SUM(economia.custo_unit) as custo_unit"),
            "economia.dad_estimado"
        ];

        $result = $this->execute($params, $field)
            ->whereBetween(
                DB::raw("TO_DATE(economia.mes, 'YYMM')"),
                [
                    DB::raw("TO_DATE(TO_CHAR(current_date , 'YYYY-01-01'), 'YYYY-MM-DD') - interval '1' year"),
                    DB::raw("TO_DATE(TO_CHAR(current_date, 'YYYY-12-31'), 'YYYY-MM-DD') ")
                ])
            ->groupBy(['mes', 'dad_estimado'])
            ->orderBy(DB::raw("mes, dad_estimado"))
            ->get();

        return collect(static::checkDate($result))->transform(fn($value) => Arr::set($value, 'mes', date_format(date_create($value['mes']), "M/Y")))->all();
    }


    public static function checkDate($value): array
    {

        $year = collect($value)->transform(fn($item, $value) => collect(Str::of($item['mes'])
            ->explode('-')->offsetGet(0)))->unique()->toArray();
        $month = collect($value)->transform(fn($item, $value) => collect(Str::of($item['mes'])
            ->explode('-')->offsetGet(1)))->unique()->toArray();

        $month_stat = end($month);
        $date_stat = current($year);
        $date_end = end($year);

        $start_date = date_create("{$date_stat[0]}-01-01");
        $end_date = date_create("{$date_end[0]}-{$month_stat[0]}-30");

        $interval = DateInterval::createFromDateString('1 months');
        $daterange = new DatePeriod($start_date, $interval, $end_date);

        $date = [];
        foreach ($daterange as $date1) {
            $date[] = $date1->format('Y-m'.'-01');
        }

        $arr = collect($value)->toArray();

        foreach ($date as $dt) {
            if (!in_array($dt, array_column($arr, 'mes'))) {
                $arr[] = ['mes' => $dt];
            }
        }

        usort($arr, function ($a, $b, $i = 'mes') {
            $t1 = strtotime($a[$i]);
            $t2 = strtotime($b[$i]);
            return $t1 - $t2;
        });

        return $arr;
    }

}
