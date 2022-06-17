<?php

namespace App\Helpers\Model\filter;

class FilterBuilderFactory
{
    public static function createFilterBuilderResponse(FilterBuilder $filter, array $data = []) : FilterBuilderResponse
    {
        $json = json_decode(json_encode($filter));
        $response = new FilterBuilderResponse();
        $response->jsonSetObject($json);
        $response->setData($data);
        return $response;
    }
}
