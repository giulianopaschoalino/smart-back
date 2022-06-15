<?php

namespace App\Support\FilterBuilder;

use App\Support\FilterBuilder\Entity\FilterItem;
use App\Support\FilterBuilder\Entity\OrderItem;
use App\Support\FilterBuilder\Interfaces\IFilterBuilder;
use Illuminate\Database\Eloquent\Builder;

class FilterQueryBuilder extends EntityJson implements IFilterBuilder
{

    protected int $limit = 10;

    protected int $offset = 0;

    protected array $filters = [];

    protected array $order = [];

    protected array $fields = [];


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
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
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

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

}