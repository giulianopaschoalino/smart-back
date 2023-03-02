<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Models\User;
use App\Repositories\AbstractRepository;

class UserRepository extends AbstractRepository implements UserContractInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getOrdered()
    {
        return $this->model->with('roles')->orderBy('name')->get();
    }
}