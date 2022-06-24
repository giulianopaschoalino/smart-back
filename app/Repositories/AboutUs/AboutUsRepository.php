<?php

declare(strict_types=1);

namespace App\Repositories\AboutUs;

use App\Models\AboutUs;
use App\Repositories\AbstractRepository;

class AboutUsRepository extends AbstractRepository implements AboutUsContractInterface
{
    public function __construct(AboutUs $aboutUs)
    {
        parent::__construct($aboutUs);
    }

}