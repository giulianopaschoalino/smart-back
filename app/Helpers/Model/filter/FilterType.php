<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers\Model\filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Description of FilterType
 * Filtros padrãos do laravel builder
 * @author renan
 */
class FilterType
{
    const WHERE_FILTER = array(
      "=",
      "<>",
      ">",
      ">=",
      "<",
      "<=",
      "like",
      "not_like"
    );

    const IN_FILTER = array(
      "in",
      "not_in"
    );

    const BETWEEN_FILTER = array(
      "between",
      "not_between"
    );

    const NULL_FILTER = array(
      "null",
      "not_null"
    );

    const DATE_FILTER = [

    ];

    public static function filter(Builder $builder, FilterItem $filter) : Builder
    {
        if (in_array($filter->getType(), self::WHERE_FILTER)) {
            return self::MAKE_WHERE_FILTER($builder, $filter);
        }
        if (in_array($filter->getType(), self::IN_FILTER)) {
            return self::MAKE_IN_FILTER($builder, $filter);
        }

        if (in_array($filter->getType(), self::BETWEEN_FILTER)) {
            return self::MAKE_BETWEEN_FILTER($builder, $filter);
        }

        if (in_array($filter->getType(), self::NULL_FILTER)) {
            return self::MAKE_NULL_FILTER($builder, $filter);
        }

        if (in_array($filter->getType(), self::DATE_FILTER)){

        }

        return $builder;
    }

    private static function MAKE_WHERE_FILTER(Builder $builder, FilterItem $filter) : Builder
    {
        $fType = $filter->getType();
        // filtro not_like sem o underscore (deixo o _ por padrao)
        if ($fType === 'not_like') {
            $fType = 'not like';
        }

        $field = ($filter->getRow()) ? DB::raw($filter->getField()) : $filter->getField();

        return $builder->where($field, $fType, $filter->getValue());
    }

    private static function MAKE_IN_FILTER(Builder $builder, FilterItem $filter) : Builder
    {
        if ($filter->getType() === "in") {
            return $builder->whereIn($filter->getField(), $filter->getValue());
        } elseif ($filter->getType() === "not_in") {
            return $builder->whereNotIn($filter->getField(), $filter->getValue());
        }

        return $builder;
    }

    private static function MAKE_BETWEEN_FILTER(Builder $builder, FilterItem $filter) : Builder
    {
        if ($filter->getType() === "between") {
            return $builder->whereBetween($filter->getField(), $filter->getValue());
        } elseif ($filter->getType() === "not_between") {
            return $builder->whereNotBetween($filter->getField(), $filter->getValue());
        }

        return $builder;
    }

    private static function MAKE_NULL_FILTER(Builder $builder, FilterItem $filter) : Builder
    {
        if ($filter->getType() === "null") {
            return $builder->whereNull($filter->getField());
        } elseif ($filter->getType() === "not_null") {
            return $builder->whereNotNull($filter->getField());
        }

        return $builder;
    }
}
