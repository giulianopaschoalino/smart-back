<?php

namespace App\Repositories\Med5min;

use App\Repositories\ContractInterface;

interface Med5minContractInterface extends ContractInterface
{
    public function getDiscretized15min($params);
    public function getDiscretizedOneHour($params);
    public function getDiscretizedOneDay($params);
    public function getPowerFactor($params);
    public function getDemand($params);

}