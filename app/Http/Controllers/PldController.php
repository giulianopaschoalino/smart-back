<?php

namespace App\Http\Controllers;

use App\Http\Resources\OverviewResource;
use App\Repositories\Pld\PldContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PldController extends Controller
{

    use ApiResponse;

    public function __construct(
        protected PldContractInterface $pldContract
    ){}

    public function overviewByRegion()
    {
        try {
            $response = $this->pldContract->getOverviewByRegion();
            return (new OverviewResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function listConsumption(Request $request)
    {
        try {
            $response = $this->pldContract->getListConsumption($request->all());
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function consumptionByDaily(Request $request)
    {
        try {
            $response = $this->pldContract->getConsumptionByDaily($request->all());
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function consumptionBySchedule(Request $request)
    {
        try {
            $response = $this->pldContract->getConsumptionBySchedule($request->all());
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }
}
