<?php

namespace App\Repositories\Economy;

use App\Repositories\ContractInterface;

interface EconomyContractInterface extends ContractInterface
{

    public function execute($params);

}