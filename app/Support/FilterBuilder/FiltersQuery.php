<?php

namespace App\Support\FilterBuilder;

use Exception;

trait FiltersQuery
{

    public function getJsonObject($json, $className)
    {
        try {
            $jsonData = json_decode(json_encode($json), false);

            $obj = new $className;

            if (!isset($jsonData) && method_exists($obj, 'jsonToObject'))
            {
                throw new Exception("Request inválido");
            }

            $obj->jsonSetObject($jsonData);

            return $obj;

        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function getFilterBuilder($json): ?FilterQueryBuilder
    {
        return $this->getJsonObject($json, FilterQueryBuilder::class);
    }

}