<?php

declare(strict_types=1);

namespace App\Repositories\Economy;

use App\Repositories\ContractInterface;

interface EconomyContractInterface extends ContractInterface
{
    public function getGrossAnnualEconomy($params);
    public function getGrossMonthlyEconomy($params);
    public function getCaptiveMonthlyEconomy($params);
    public function getCostMWhEconomy($params);


}