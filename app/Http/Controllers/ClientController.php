<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Helpers\ResponseJsonMessage;
use App\Repositories\DadosCadastrais\DadosCadastraisContractInterface;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        protected DadosCadastraisContractInterface $dadosCadastraisContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->dadosCadastraisContract->search($request->all());

        return ResponseJsonMessage::withData($response);
    }
}
