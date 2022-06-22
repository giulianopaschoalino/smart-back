<?php

declare(strict_types=1);

namespace App\Repositories\DadosCadastrais;

use App\Models\DadosCadastrais;
use App\Models\DadosTe;
use App\Repositories\AbstractRepository;



class DadosCadastraisRepository extends AbstractRepository implements DadosCadastraisContractInterface
{

    public function __construct(DadosCadastrais $dadosCadastrais)
    {
        parent::__construct($dadosCadastrais);
    }
}
