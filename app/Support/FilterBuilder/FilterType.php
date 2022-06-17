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

    public static function filter(Builder $builder, FilterItem $filter): Builder
    {

        if (in_array($filter->getType(), self::WHERE_FILTER)) {
            return static::makeWhereFilter($builder, $filter);
        }

        if (in_array($filter->getType(), self::IN_FILTER)) {
            return static::makeInFilter($builder, $filter);
        }

        if (in_array($filter->getType(), self::BETWEEN_FILTER)) {
            return static::makeBetweenFilter($builder, $filter);
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

    private static function makeInFilter(Builder $builder, FilterItem $filter) : Builder
    {
        if ($filter->getType() === "in") {
            return $builder->whereIn($filter->getField(), $filter->getValue());
        } elseif ($filter->getType() === "not_in") {
            return $builder->whereNotIn($filter->getField(), $filter->getValue());
        }

        return $builder;
    }

    private static function makeBetweenFilter(Builder $builder, FilterItem $filter): Builder
    {
        if ($filter->getType() === "between") {
            return $builder->whereBetween($filter->getField(), $filter->getValue());
        } elseif ($filter->getType() === "not_between") {
            return $builder->whereNotBetween($filter->getField(), $filter->getValue());
        }

        return $builder;
    }


}