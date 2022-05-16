<?php

namespace App\Actions;

use App\Interface\ActionInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserAction implements ActionInterface
{

    public function list(): User
    {
        return User::all();
    }

    public function create(array $params): Builder|Model
    {
        return User::query()->create($params);
    }

    public function show($id): User
    {
        return User::query()->find($id);
    }

    public function update(array $params, $id): int
    {
        return User::query()->find($id)->update($params);
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}
