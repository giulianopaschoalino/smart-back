<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Helper\Model\filter;

class FieldFilterBuilder extends FilterBuilder
{
    /**
     * @var string[]
     */
    protected array $fields = [];

    public function getFields() : array
    {
        return $this->fields;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }
}
