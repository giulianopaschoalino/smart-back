<?php

namespace App\Support\FilterBuilder;

use App\Support\FilterBuilder\Entity\FilterItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FilterType
{
    const WHERE_FILTER = [
        "=",
        "<>",
        ">",
        ">=",
        "<",
        "<=",
        "like",
        "not_like"
    ];

    const IN_FILTER = [
        "in",
        "not_in"
    ];

    const BETWEEN_FILTER = [
        "between",
        "not_between"
    ];

    const NULL_FILTER = [
        "null",
        "not_null"
    ];

    public static function filter(Builder $builder, FilterItem $filter) : Builder
    {

        if (in_array($filter->getType(), self::WHERE_FILTER))
        {
            return static::makeWhereFilter($builder, $filter);
        }

        return $builder;
    }

    public static function makeWhereFilter(Builder $builder, FilterItem $filter): Builder
    {
        $fType = $filter->getType();

        if ($fType === 'not_like') {
            $fType = 'not like';
        }

        $field = ($filter->getRow()) ? DB::raw($filter->getField()) : $filter->getField();

        return $builder->where($field, $fType, $filter->getValue());

    }


}