<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Helpers\ResponseJson;
use App\Repositories\DadosCadastrais\DadosCadastraisContractInterface;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DadosCadastraisContractInterface $dadosCadastraisContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->dadosCadastraisContract->search($request->all());

        return ResponseJson::data($response);
    }
}
