<?php

namespace App\Support\FilterBuilder;

use App\Support\FilterBuilder\Entity\FieldItem;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class FieldType
{
    const FUNCTION_FIELD = [
        "TO_CHAR"
    ];

    public static function field(FieldItem $field)
    {
        if (in_array($field->getType(), self::FUNCTION_FIELD)) {
            return static::makeFormatFieldDate($field);
        }

        return $field->getField();
    }

    public static function makeFormatFieldDate($field): Expression
    {
        return DB::raw("TO_CHAR(TO_DATE({$field->field}, 'YYMM'), '{$field->format}') as {$field->field}");
    }


}