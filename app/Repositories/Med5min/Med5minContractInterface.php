<?php

namespace App\Repositories\Med5min;

use App\Repositories\ContractInterface;

interface Med5minContractInterface extends ContractInterface
{
    public function discretized5min($params);
    public function discretized15min($params);
    public function discretizedOneHour($params);
    public function discretizedOneDay($params);

}