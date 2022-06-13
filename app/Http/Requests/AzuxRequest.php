<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Requests;

use Exception;
use Illuminate\Http\Request;
use App\Helper\Model\filter\FieldFilterBuilder;
use App\Helper\Model\filter\FilterBuilder;

/**
 * Description of AzuxRequest
 *
 * @author renan
 */
class AzuxRequest extends Request
{
    public function getJsonObject($className)
    {
        $jsonData = json_decode($this->getContent());
        if (isset($jsonData)) {
            $obj = new $className;
            if (method_exists($obj, 'jsonToObject')) {
                $obj->jsonSetObject($jsonData);
                return $obj;
            }
        } else {
            throw new Exception("Request inválido");
        }
    }

    public function getFilterBuilder() : ?FilterBuilder
    {
        return $this->getJsonObject(FilterBuilder::class);
    }

    public function getFieldFilterBuilder() : ?FieldFilterBuilder
    {
        return $this->getJsonObject(FieldFilterBuilder::class);
    }
}
