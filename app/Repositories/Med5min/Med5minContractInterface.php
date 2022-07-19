<?php

declare(strict_types=1);

namespace App\Repositories\Med5min;

use App\Repositories\ContractInterface;

interface Med5minContractInterface extends ContractInterface
{
    public function getDiscretized5min($params, bool $typeField);
    public function getDiscretized15min($params, bool $typeField);
    public function getDiscretizedOneHour($params, bool $typeField);
    public function getDiscretizedOneDay($params, bool $typeField);
    public function getDiscretizedOneMonth($params, bool $typeField);
    public function getDiscretization($params, $path);

}