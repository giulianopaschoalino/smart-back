<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Helper\Model\filter;

class FilterBuilderResponse extends FilterBuilder
{
    /**
     * @var array
     */
    protected $data = [];
    
    /**
     * @var int
     */
    protected $total = 0;


    public function getData() : array
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
    
    public function getTotal() : int
    {
        return $this->total;
    }

    public function setTotal(int $total)
    {
        $this->total = $total;
    }
}
