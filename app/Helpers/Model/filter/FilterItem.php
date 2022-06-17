<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Helpers\Model\filter;


use App\Helpers\Model\EntityJson;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * FilterItem
 *
 * Objeto utilizado para base dos filtros, por padrão ele possui um estrutura json nesse formato
 * { type: "=", field: "id_produto", value: 18832 }
 * @author renan
 */

/**
 * @OA\Schema(
 *      schema="FilterItemFilter",
 *      type="object",
 *      description="FilterItemFilter entity",
 *      title="FilterItemFilter entity",
 *      @OA\Property(property="type", type="string", example=">=", description="Tipo do filtro podendo ser: =,<>,>,>=,<,<=,between,not_between,like,not_like,in,not_in"),
 *      @OA\Property(property="field", type="string", description="Campo a ser ordernado (tem ser um campo valido)"),
 *      @OA\Property(property="value", type="string", example="30", description="Valor a ser filtrado. No between e in usar array nos outros casos usar um valor normal (sem ser numerico)."),
 * )
 *
 * @OA\Schema(
 *     schema="FilterItemFilterList",
 *     allOf={
 *          @OA\Schema(ref="#/components/schemas/FilterItemFilter")
 *     }
 * )
 */
class FilterItem extends EntityJson
{
    /**
     * @var string
     */
    protected $type = "=";

    /**
     * @var string
     */
    protected $field;

    /**
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected bool $row = false;

    /**
     * @return bool
     */
    public function getRow(): bool
    {
        return $this->row;
    }

    /**
     * @param bool $row
     */
    public function setRow(bool $row): void
    {
        $this->row = $row;
    }


    public function getType() : string
    {
        return $this->type;
    }

    public function getField() :Expression|string
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function setField($field)
    {
        $this->field = $field;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}
