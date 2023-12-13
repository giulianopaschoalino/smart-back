<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\EconomyResource;
use App\Repositories\Economy\EconomyContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EconomyController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected EconomyContractInterface $economyContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->economyContract->search($request->all());

        return response()->json($response, Response::HTTP_OK);
    }

    public function grossAnnualEconomy(Request $request): JsonResponse
    {
        $response = $this->economyContract->getGrossAnnualEconomy($request->all());

        return response()->json($response, Response::HTTP_OK);
    }

    public function grossMonthlyEconomy(Request $request): JsonResponse
    {

        $response = $this->economyContract->getGrossMonthlyEconomy($request->all());

        return response()->json($response, Response::HTTP_OK);
    }

    public function captiveMonthlyEconomy(Request $request): JsonResponse
    {
        $response = $this->economyContract->getCaptiveMonthlyEconomy($request->all());

        return response()->json($response, Response::HTTP_OK);
    }

    public function costMWhEconomy(Request $request): JsonResponse
    {
        $response = $this->economyContract->getCostMWhEconomy($request->all());

        return response()->json($response, Response::HTTP_OK);
    }
}
