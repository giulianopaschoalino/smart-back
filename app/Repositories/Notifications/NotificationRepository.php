<?php

namespace App\Repositories\Notifications;

use App\Models\Notifications;
use App\Repositories\AbstractRepository;

class NotificationRepository extends AbstractRepository implements NotificationContractInterface
{

    public function __construct(Notifications $notification)
    {
        parent::__construct($notification);
    }

}