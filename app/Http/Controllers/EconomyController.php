<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJson;
use App\Repositories\Economy\EconomyContractInterface;
use App\Traits\ApiResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return ResponseJson::data($response);
    }

    public function grossAnnualEconomy(Request $request): JsonResponse
    {
        $response = $this->economyContract->getGrossAnnualEconomy($request->all());

        return ResponseJson::data($response);
    }

    public function grossMonthlyEconomy(Request $request): JsonResponse
    {

        $response = $this->economyContract->getGrossMonthlyEconomy($request->all());

        return ResponseJson::data($response);
    }

    public function captiveMonthlyEconomy(Request $request): JsonResponse
    {
        $response = $this->economyContract->getCaptiveMonthlyEconomy($request->all());

        return ResponseJson::data($response);
    }

    public function costMWhEconomy(Request $request): JsonResponse
    {
        $response = $this->economyContract->getCostMWhEconomy($request->all());

        return ResponseJson::data($response);
    }
}
