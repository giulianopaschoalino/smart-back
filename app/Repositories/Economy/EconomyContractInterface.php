<?php

namespace App\Repositories\Economy;

use App\Repositories\ContractInterface;

interface EconomyContractInterface extends ContractInterface
{

    public function getGrossEconomy($params);
    public function getAccumulatedEconomy($params);
    public function getCostEstimatesEconomy($params);
    public function getCostMWhEconomy($params);


}