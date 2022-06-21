<?php

if (!function_exists('checkUserId')) {
    function checkUserId($client_id): bool
    {
        return \auth()->hasUser() && !is_null($client_id);
    }
}

if (!function_exists('stats_standard_deviation')) {
    function stats_standard_deviation(array $a, $sample = false): float|bool
    {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double)$val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
            --$n;
        }
        return sqrt($carry / $n);
    }
}
