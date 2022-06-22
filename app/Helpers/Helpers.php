<?php


namespace App\Helpers;

class Helpers
{
    public static function uploadFiles($params, $field): ?string
    {
        $result = null;
        if ($params->hasFile($field))
        {
            $result = url('storage') . '/' . $params->file($field)->store('users');
        }
        return $result;
    }

}
