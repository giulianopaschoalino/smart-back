<?php

namespace App\Repository;

use App\Helper\Model\filter\FilterBuilder;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{

    protected Model $model;

    abstract protected function model();

    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

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

    public function __get($name)
    {
        return $this->model->{$name};
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    protected function resolveModel(): Model
    {
        $model = app()->make($this->model());

        if (!$model instanceof Model) {
            throw new Exception(
                "Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }
        return $model;
    }

}
