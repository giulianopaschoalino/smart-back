<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

    public function search($params)
    {
        $filter = static::getFilterBuilder($params);

        $query = $this->model->select($filter->getFields());

        return $filter->applyFilter($query)->get();
    }
}