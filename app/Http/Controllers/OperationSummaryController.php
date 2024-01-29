<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Helpers;

use App\Helpers\ResponseJsonMessage;
use App\Repositories\DadosTe\DadosTeContractInterface;

use Illuminate\Http\Request;

class OperationSummaryController extends Controller
{
    

    public function __construct(
        protected DadosTeContractInterface $dadosTeContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->dadosTeContract->search($request->all(), true);
        $response = Helpers::orderByDate($response, 'm/Y');

        return ResponseJsonMessage::withData($response);
    }

    public function operationSummary(Request $request)
    {
        $response = $this->dadosTeContract->getOperationSummary($request->all());

        return ResponseJsonMessage::withData($response);
    }
}
