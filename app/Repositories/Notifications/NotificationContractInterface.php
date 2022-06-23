<?php

namespace App\Repositories\Notifications;

use App\Repositories\ContractInterface;

interface NotificationContractInterface extends ContractInterface
{
    public function getNotify();

}