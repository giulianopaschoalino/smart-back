<?php

declare(strict_types=1);

namespace App\Support\FilterBuilder;

use App\Support\FilterBuilder\Entity\FieldItem;
use App\Support\FilterBuilder\Entity\FilterItem;
use App\Support\FilterBuilder\Entity\OrderItem;
use App\Support\FilterBuilder\Interfaces\IFilterBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FilterQueryBuilder extends EntityJson implements IFilterBuilder
{

    protected int $limit = 20;

    protected int $offset = 0;

    protected array $filters = [];

    protected array $order = [];

    protected array $fields = [];

    protected bool $distinct = false;


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


    public function applyField($fields = [])
    {
        if (!empty($this->getFields())) {
            foreach ($this->getFields() as $field)
            {
                $fields[] = FieldType::field($field);
            }
        }
        return $fields;
    }


    /**
     */
    public function format_date_sql(): static
    {
        foreach ($this->getFields() as $param) {
            $this->setFields([DB::raw("TO_CHAR(TO_DATE({$param}, 'YYMM'), 'MM/YYYY') as {$param}")]);
        }
        return $this;
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
        $this->fields = $this->arrayObjectCast($fields, FieldItem::class);
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

    /**
     * @return bool
     */
    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    /**
     * @param bool $distinct
     */
    public function setDistinct(bool $distinct): void
    {
        $this->distinct = $distinct;
    }





}