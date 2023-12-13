<?php


namespace App\Helpers;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Helpers
{
    public static function uploadFiles($params, $field): ?string
    {
        $result = null;
        if ($params->hasFile($field)) {
            $result = url('storage') . '/' . $params->file($field)->store('users');
        }
        return $result;
    }

    public static function orderByDate($result, $format = 'M/Y', $field = 'mes'): array
    {
        return collect($result)
            ->transform(fn ($value) => Arr::set(
                $value,
                $field,
                Carbon::createFromFormat('ym', $value[$field])->locale('pt-BR')
                    ->translatedFormat($format)
            ))
            ->all();
    }

    public static function formatOfFooter($result, $format = 'd/m/Y H:i', $field = 'day_formatted'): array
    {
        return collect($result)->transform(function ($item) use ($field, $format) {

            $date = $item->day_formatted;
            $hor = $item->hora;
            $min = ($item->minut % 60);

            $min = $min < 10 ? "0{$min}" : $min;

            $hor = $hor < 10 ? "0{$hor}" : $hor;

            return Arr::set($item, $field, Carbon::createFromFormat('d/m/Y Hi', $date . ' ' . $hor . $min)->locale('pt-BR')
                ->translatedFormat($format));
        })->all();
    }

    public static function checkDate($value): array
    {

        $year = collect($value)
            ->transform(
                fn ($item, $value) => collect(Str::of($item['mes'])
                    ->explode('-')
                    ->offsetGet(0))
            )
            ->unique()
            ->toArray();

        $month = collect($value)
            ->transform(
                fn ($item, $value) => collect(Str::of($item['mes'])
                    ->explode('-')
                    ->offsetGet(1))
            )
            ->unique()
            ->toArray();

        $month_stat = end($month);
        $date_stat = current($year);
        $date_end = end($year);

        if (!$date_stat) return [];

        $start_date = date_create("{$date_stat[0]}-01-01");
        $end_date = date_create("{$date_end[0]}-{$month_stat[0]}-30");

        $interval = DateInterval::createFromDateString('1 months');
        $daterange = new DatePeriod($start_date, $interval, $end_date);

        $date = [];
        foreach ($daterange as $date1) {
            $date[] = $date1->format('Y-m' . '-01');
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
