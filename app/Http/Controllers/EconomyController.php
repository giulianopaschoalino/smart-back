<?php

namespace App\Http\Controllers;

use App\Models\DadosCadastrais;
use App\Models\Economy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EconomyController extends Controller
{

    public function index(): JsonResponse
    {

        abort_if(Gate::denies('teste-index'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        $result = DadosCadastrais::query()->limit(10)->get();

        return response()->json($result, 200);

    }

}
