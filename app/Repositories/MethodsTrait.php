<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;

trait MethodsTrait
{
    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $params)
    {
       return $this->model->create($params);
    }

    public function update(array $params, $id)
    {
        ($model = $this->model->findOrFail($id))->update($params);
        return $model;
    }

    public function destroy($id)
    {
        return $this->model::find($id)->delete();
    }

    public function withRelationsByAll($relations): Collection|array
    {
        return $this->model->with($relations)->get();
    }


    /**
     * @throws BindingResolutionException
     */
    public function search($params, $rowField = false)
    {
        $this->filterBuilder($params);

        $fields = $this->item->getFields();

        $query = $this->model->select($fields);

        $response = $this->item->applyFilter($query);

        if ($this->item->isDistinct()){
            $response = $response->distinct();
        }

       return $response->get();
    }
}