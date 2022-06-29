<?php

declare(strict_types=1);

namespace App\Interface;

interface RepositoryInterfaces
{
    public function list();
    public function create(array $params);
    public function find(int $id);
    public function update(array $params, int $id);
    public function delete(int $id);

}
