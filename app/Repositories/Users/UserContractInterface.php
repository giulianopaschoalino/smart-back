<?php

namespace App\Repositories\Users;

use App\Repositories\ContractInterface;

interface UserContractInterface extends ContractInterface
{
    public function getOrdered();
}