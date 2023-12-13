<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Traits\ApiResponse;
use App\Helpers\ResponseJson;
use App\Repositories\DadosTe\DadosTeContractInterface;

use Illuminate\Http\Request;

class OperationSummaryController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DadosTeContractInterface $dadosTeContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->dadosTeContract->search($request->all(), true);
        $response = Helpers::orderByDate($response, 'm/Y');

        return ResponseJson::data($response);
    }

    public function operationSummary(Request $request)
    {
        $response = $this->dadosTeContract->getOperationSummary($request->all());

        return ResponseJson::data($response);
    }
}
