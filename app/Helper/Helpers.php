<?php


namespace App\Helper;

class Helpers
{
    public static function object_to_array($object)
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        return array_map([__CLASS__, 'object_to_array'], (array) $object);
    }

    public static function array_enkeyize($array, $iten): array
    {
        $keized = [];
        foreach ($array as $key => $value) {
            foreach ($value as $v_key => $v_value) {
                if ($v_key === $iten) {
                    $keized[$v_value] = $array[$key];
                }
            }
        }
        return $keized;
    }
}
