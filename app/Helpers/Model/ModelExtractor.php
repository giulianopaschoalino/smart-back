<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Description of ModelExtractor
 *
 * @author renan
 */
class ModelExtractor extends TableExtractor
{
    
    /**
     *
     * @var string
     */
    private $modelClass = '';
    
    /**
     * @var Model
     */
    private $model;
    
    
    public function __construct(string $modelClass)
    {
        parent::__construct();
        $this->modelClass = $modelClass;
        $this->instanceModel();
    }
    
    public function getAttributeFromMethod($method, array $parameters = [])
    {
        if (method_exists($this->model, $method)) {
            if (count($parameters) > 0) {
                $result = [$this->model, $method](...$parameters);
            } else {
                $result = [$this->model, $method]();
            }
            if (isset($result)) {
                if ($result instanceof $this->modelClass) {
                    $this->setAttributeFromDataArray($result->toArray(), $this->model->getCasts());
                    return $this->getAttributes();
                } elseif ($result instanceof Collection) {
                    $this->setAttributeFromCollection($result);
                    return $this->getAttributes();
                } elseif (is_array($result)) {
                    $this->setAttributeFromDataArray((array) $result[0], $this->model->getCasts());
                    return $this->getAttributes();
                } elseif (is_object($result)) {
                    $this->setAttributeFromDataArray((array) $result, $this->model->getCasts());
                    return $this->getAttributes();
                } else {
                    throw new \Exception('Nao foi possivel converter esse tipo de retorno verificar ');
                }
            }
        } else {
            throw new \Exception('Method not found for this class');
        }
    }

    private function setAttributeFromCollection(Collection $collection)
    {
        $casts = $this->model->getCasts();
        $this->setAttributeFromDataArray($collection->toArray()[0], $casts);
    }
    
    private function setAttributeFromDataArray(array $data, array $casts = [])
    {
        $attributes = [];
        foreach ($data as $key => $value) {
            if (!empty($casts) && isset($casts[$key])) {
                $attributes[] = $this->createColumn($key, $casts[$key]);
            } else {
                $attributes[] = $this->createColumn($key, $this->getDataTypeByValue($value));
            }
        }
        
        $this->attributes = $attributes;
    }

    private function instanceModel()
    {
        $this->model = new $this->modelClass;
        $this->setTable($this->model->getTable());
        $this->setOwner($this->model->getConnectionName());
    }
}
