<?php

declare(strict_types=1);

namespace App\Support\FilterBuilder\Entity;

use App\Support\FilterBuilder\EntityJson;
use Illuminate\Database\Query\Expression;

class FilterItem extends EntityJson
{
    /**
     * @var string
     */
    protected string $type = "=";

    /**
     * @var string
     */
    protected string $field;

    /**
     *
     * @var mixed
     */
    protected mixed $value;

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

    public function setField($field): void
    {
        $this->field = $field;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }
}