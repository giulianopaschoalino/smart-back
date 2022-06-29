<?php

declare(strict_types=1);

namespace App\Support\FilterBuilder\Entity;

use App\Support\FilterBuilder\EntityJson;

/**
 * OrderItem
 *
 * Objeto utilizado para base dos filtros, por padrão ele possui um estrutura json nesse formato
 * { field: "id_produto", direction: "asc" }
 * @author renan
 */

class OrderItem extends EntityJson
{

    protected string $field;

    protected string $direction = "asc";
       

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

    public function setDirection(string $direction = "asc")
    {
        $this->direction = $direction;
    }
}
