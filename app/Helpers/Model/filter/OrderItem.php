<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Helpers\Model\filter;

use App\Entity\EntityJson;

/**
 * OrderItem
 *
 * Objeto utilizado para base dos filtros, por padrão ele possui um estrutura json nesse formato
 * { field: "id_produto", direction: "asc" }
 * @author renan
 */

/**
 * @OA\Schema(
 *      schema="OrderItemFilter",
 *      type="object",
 *      description="OrderItemFilter entity",
 *      title="OrderItemFilter entity",
 *      @OA\Property(property="field", type="string", description="Campo a ser ordernado (tem ser um campo valido)"),
 *      @OA\Property(property="direction", type="string", example="asc", description="Direção da ordenação (asc|desc)"),
 * )
 *
 * @OA\Schema(
 *     schema="OrderItemFilterList",
 *     allOf={
 *          @OA\Schema(ref="#/components/schemas/OrderItemFilter")
 *     }
 * )
 */

class OrderItem extends EntityJson
{
    
    /**
     * @var string
     */
    protected $field;
    
    /**
     *
     * @var string
     */
    protected $direction = "asc";
       

    public function getField() : string
    {
        return $this->field;
    }

    public function setField(string $field)
    {
        $this->field = $field;
    }
    
    public function getDirection() : string
    {
        return $this->direction;
    }

    public function setDirection(string $direction="asc")
    {
        $this->direction = $direction;
    }
}
