<?php

declare(strict_types=1);

namespace App\Repositories\Pld;

use App\Repositories\ContractInterface;

interface PldContractInterface extends ContractInterface
{
    public function getOverviewByRegion();
    public function getConsumptionByDaily($params);
    public function getListConsumption($params);
    public function getConsumptionBySchedule($params);
}