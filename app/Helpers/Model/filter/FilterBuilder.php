<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers\Model\filter;

use App\Helpers\Model\EntityJson;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @OA\Schema(
 *      schema="FilterBuilder",
 *      type="object",
 *      description="FilterBuilder entity",
 *      title="FilterBuilder entity",
 *      @OA\Property(property="filters", type="object", ref="#/components/schemas/FilterItemFilterList"),
 *      @OA\Property(property="limit", type="integer", example="10",description="Limit da paginação"),
 *      @OA\Property(property="offset", type="integer", example="0", description="Valor da offset da paginação"),
 *      @OA\Property(property="order", type="object", ref="#/components/schemas/OrderItemFilterList"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="FilterBuilderResponse",
 *     type="object",
 *     description="FilterBuilderReponse entity",
 *     title="FilterBuilderReponse entity",
 *     allOf={
 *          @OA\Schema(ref="#/components/schemas/FilterBuilder")
 *     },
 *     @OA\Property(property="data", type="array", type="object", example="[{...}]", description="Array de objetos do resultado da query"),
 *     @OA\Property(property="total", type="integer", example="100", description="Resultado do total de linhas sem a paginação"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="FieldFilterBuilder",
 *     type="object",
 *     description="FieldFilterBuilder entity",
 *     title="FieldFilterBuilder entity",
 *     allOf={
 *          @OA\Schema(ref="#/components/schemas/FilterBuilder")
 *     },
 *     @OA\Property(property="fields", type="array", type="string", example="['cd_pessoa','nm_pessoa']"),
 * )
 */
class FilterBuilder extends EntityJson implements IFilterBuilder
{
    /**
     * @var FilterItem[]
     */
    protected array $filters = [];
    /**
     * @var int
     */
    protected int $limit = 10;
    /**
     * @var int
     */
    protected int $offset = 0;

    /**
     * @var OrderItem[]
     */
    protected array $order = [];

    /**
     * @var string[]
     */
    protected array $fields = [];


    public function applyFilter(Builder $builder): Builder
    {
        if (empty($this->getFilters()) === false) {
            foreach ($this->getFilters() as $filter) {
                $builder = FilterType::filter($builder, $filter);
            }
        }
        // estilo antigo
        //$builder->limit($this->getLimit());
        //$builder->offset($this->getOffset());

        /**
         * Estilo novo com paginate
         * Vantagem q o paginate já retorna o total
         */
        $currentPage = $this->getOffset();
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        foreach ($this->getOrder() as $order) {
            $builder->orderBy($order->getField(), $order->getDirection());
        }
        return $builder;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function setOrder(array $orders)
    {
        $this->order = $this->arrayObjectCast(OrderItem::class, $orders);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $this->arrayObjectCast(FilterItem::class, $filters);
    }

    public function addFilter(FilterItem $filter)
    {
        $this->filters[] = $filter;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    public function getFields() : array
    {
        return $this->fields;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }
}
