<?php

namespace App\Interface;

interface ActionInterface
{
    public function list();
    public function create(array $params);
    public function show($id);
    public function update(array $params, $id);
    public function delete($id);

}
