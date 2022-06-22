<?php

namespace App\Repositories;

interface ContractInterface
{
    public function all();
    public function find($id);
    public function create(array $params);
    public function update(array $params, $id);
    public function destroy($id);
    public function withRelationsByAll($relations);
    public function search($param);
}
