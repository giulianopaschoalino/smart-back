<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\EconomyResource;
use App\Repositories\Economy\EconomyContractInterface;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EconomyController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected EconomyContractInterface $economyContract
    )
    {
    }

    public function index(Request $request)
    {

        try {
            $response = $this->economyContract->selectGlobal($request->all());
            return (new EconomyResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function grossAnnualEconomy(Request $request): JsonResponse
    {
        try {
            $response = $this->economyContract->getGrossAnnualEconomy($request->all());
            return (new EconomyResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function grossMonthlyEconomy(Request $request): JsonResponse
    {
        try {
            $response = $this->economyContract->getGrossMonthlyEconomy($request->all());
            return (new EconomyResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function captiveMonthlyEconomy(Request $request): JsonResponse
    {
        try {
            $response = $this->economyContract->getCaptiveMonthlyEconomy($request->all());
            return (new EconomyResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function costMWhEconomy(Request $request): JsonResponse
    {
        try {
            $response = $this->economyContract->getCostMWhEconomy($request->all());
            return (new EconomyResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
