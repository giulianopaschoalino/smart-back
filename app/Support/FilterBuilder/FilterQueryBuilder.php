<?php

namespace App\Support\FilterBuilder;

use App\Support\FilterBuilder\Entity\FilterItem;
use App\Support\FilterBuilder\Entity\OrderItem;
use App\Support\FilterBuilder\Interfaces\IFilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class FilterQueryBuilder extends EntityJson implements IFilterBuilder
{

    protected array $filters = [];

    protected array $order = [];

    public function applyFilter(Builder $builder): Builder
    {
        if (!empty($this->getFilters())) {

            foreach ($this->getFilters() as $filter) {

                $builder = FilterType::filter($builder, $filter);
            }
        }

        if (!empty($this->limit) && !empty($this->offset)){
            $builder->limit($this->limit);
            $builder->offset($this->offset);
        }

        foreach ($this->getOrder() as $order) {
            $builder->orderBy($order->getField(), $order->getDirection());
        }

        return $builder;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $this->arrayObjectCast($filters, FilterItem::class);
    }

    /**
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * @param array $order
     */
    public function setOrder(array $order): void
    {
        $this->order = $this->arrayObjectCast($order,OrderItem::class);
    }



}