<?php

declare(strict_types=1);

namespace App\Repositories;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class AbstractRepository
{
    use MethodsTrait, ForwardsCalls;

    protected AbstractRepository|Model $model;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(Model $model = null)
    {
        $this->model = $model ?? $this::resolveModel();
    }

    public function __call($name, $arguments)
    {
        $result = $this->forwardCallTo($this->model, $name, $arguments);

        if ($result === $this->model) {
            return $this;
        }

        return $result;
    }

    public function __get($name)
    {
        return $this->model->{$name};
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    final protected function resolveModel(): AbstractRepository
    {
        $model = app()->make($this->model);

        if (!$model instanceof Model) {
            throw new Exception(
                "Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }

        return (new static)->$model;
    }

}
