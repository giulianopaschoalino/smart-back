<?php

declare(strict_types=1);

namespace App\Repositories\Economy;

use App\Models\Economy;
use App\Repositories\AbstractRepository;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    public function getGrossAnnualEconomy($params): Collection|array
    {
        $field = [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'YYYY') as ano"),
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

    public function getGrossMonthlyEconomy($params)
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

    public function getCaptiveMonthlyEconomy($params)
    {
        $field = [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY') as mes"),
            DB::raw("SUM(economia.custo_cativo) as custo_cativo"),
            DB::raw("SUM(economia.custo_livre) as custo_livre"),
            DB::raw("SUM(economia.economia_mensal) as economia_mensal"),
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
            ->get();
    }

    public function getCostMWhEconomy($params)
    {
        $field = [
            DB::raw("TO_CHAR(TO_DATE(economia.mes, 'YYMM'), 'MM/YYYY') as mes"),
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
            ->get();

        return static::checkDate($result);
    }


    public static function checkDate($value): array
    {

        $val = collect($value)->transform(fn($item, $value) => collect(Str::of($item['mes'])->explode('/')->offsetGet(1)))->unique()->toArray();

        $date_stat = current($val);
        $date_end = end($val);
        $start_date = date_create("{$date_stat[0]}-01-01");
        $end_date = date_create("{$date_end[0]}-03-30"); // If you want to include this date, add 1 day

        $interval = DateInterval::createFromDateString('1 months');
        $daterange = new DatePeriod($start_date, $interval, $end_date);

        $date = [];
        foreach ($daterange as $date1) {
            $date[] = $date1->format('m/Y');
        }

        $arr = collect($value)->toArray();

        $i = 0;
        foreach ($date as $dt) {
            if (empty($arr[$i])) {
                $arr[] = ['mes' => $dt];
            }
            $i++;
        }
        sort($arr);

        return $arr;

    }




}
