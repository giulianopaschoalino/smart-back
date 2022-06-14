<?php

namespace App\Support\FilterBuilder\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface IFilterBuilder
{
    public function applyFilter(Builder $builder) : Builder;
}
