<?php

declare(strict_types=1);

namespace App\Support\FilterBuilder\Entity;

use App\Support\FilterBuilder\EntityJson;

class FieldItem extends EntityJson
{

    /**
     * @var string|null
     */
    protected string|null $type = null;

    /**
     * @var string|null
     */
    protected  string|null $format = null;

    /**
     * @var string
     */
    protected string $field;

    /**
     * @return string|null
     */
    public function getType(): string|null
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string|null $format
     */
    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }


    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }


}
