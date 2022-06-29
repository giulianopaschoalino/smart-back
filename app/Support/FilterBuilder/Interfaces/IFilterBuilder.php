<?php

declare(strict_types=1);

namespace App\Support\FilterBuilder\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface IFilterBuilder
{
    public function applyFilter(Builder $builder) : Builder;
}
