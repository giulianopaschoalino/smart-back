<?php

namespace App\Repositories\DadosTe;

use App\Repositories\ContractInterface;

interface DadosTeContractInterface extends ContractInterface
{

    public function getOperationSummary($params);

}