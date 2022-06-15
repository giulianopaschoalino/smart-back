<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
    )
    {
    }

    public function grossEconomy(Request $request): JsonResponse
    {
        try {
            $response = $this->economyContract->getGrossEconomy($request->all());

            return $this->successResponse($response);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function accumulatedEconomy(Request $request): JsonResponse
    {
        try {
            $this->economyContract->getAccumulatedEconomy($request->all());
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function costEstimatesEconomy(Request $request): JsonResponse
    {
        try {
            $this->economyContract->getCostEstimatesEconomy($request->all());
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function costMWhEconomy(Request $request): JsonResponse
    {
        try {
            $response = $this->economyContract->getCostMWhEconomy($request->all());
            return $this->successResponse($response);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function __invoke(Request $request)
    {
        try {
            $this->economyContract->execute($request);
        } catch (\Exception $exception) {
            return \response()->json([], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

//    public function index(): JsonResponse
//    {
//
//        Economy::query()->select();
//        abort_if(Gate::denies('teste-index'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');
//
//        $result = DadosCadastrais::query()->limit(10)->get();
//
//        return response()->json($result, 200);
//
//    }
//
//
//    public function teste(Request $request): JsonResponse
//    {
//        try {
//            $data = (new EconomyRepository())->execute($request->all());
//            return \response()->json($data, ResponseAlias::HTTP_OK);
//        } catch (\Exception $exception) {
//            return \response()->json([], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
//        }
//
//    }

}
