<?php

namespace App\Repository;

use App\Helper\Model\filter\FilterBuilder;
use App\Interface\RepositoryInterfaces;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class Repository extends AbstractRepository implements RepositoryInterfaces
{

    public function list(): Collection
    {
       return $this->model->all();
    }

    public function create(array $params)
    {
        return $this->model->create($params);
    }

    public function find(int $id)
    {
        // TODO: Implement find() method.
    }

    public function update(array $params, int $id)
    {
        return $this->model->find($id)->update( $params);
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function getFilterBuilder($json): ?FilterBuilder
    {
        return $this->getJsonObject($json, FilterBuilder::class);
    }


    static function logQuery($query, $type = 'get')
    {
        DB::enableQueryLog();
        $query->$type();
        dd(DB::getQueryLog());
    }


}
