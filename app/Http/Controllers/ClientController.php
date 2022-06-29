<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\DadosCadastraisResponse;
use App\Repositories\DadosCadastrais\DadosCadastraisContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DadosCadastraisContractInterface $dadosCadastraisContract)
    {
    }

    public function index(Request $request)
    {
        try {
            $response = $this->dadosCadastraisContract->search($request->all());
            return (new DadosCadastraisResponse($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}