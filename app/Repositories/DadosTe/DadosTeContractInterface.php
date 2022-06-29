<?php

declare(strict_types=1);

namespace App\Repositories\DadosTe;

use App\Repositories\ContractInterface;

interface DadosTeContractInterface extends ContractInterface
{

    public function getOperationSummary($params);

}