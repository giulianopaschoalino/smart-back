<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Helper\Model\filter;

use Illuminate\Database\Eloquent\Builder;

interface IFilterBuilder
{
    public function applyFilter(Builder $builder) : Builder;
}
