<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Resources\OperationSummaryResource;
use App\Repositories\DadosTe\DadosTeContractInterface;
use App\Traits\ApiResponse;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OperationSummaryController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DadosTeContractInterface $dadosTeContract
    )
    {
    }

    public function index(Request $request)
    {
        try {
            $response = $this->dadosTeContract->search($request->all(), true);
            $response = Helpers::orderByDate($response, 'm/Y');
            return (new OperationSummaryResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function operationSummary(Request $request)
    {
        try {
            $response = $this->dadosTeContract->getOperationSummary($request->all());
            return (new OperationSummaryResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
