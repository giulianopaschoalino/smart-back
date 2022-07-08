<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Resources\PldResource;
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

    public function index(Request $request)
    {
        try {
            $response = $this->pldContract->search($request->all());
            $response = Helpers::orderByDate($response, 'm/Y', 'mes_ref');
            return (new PldResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function overviewByRegion()
    {
        try {
            $response = $this->pldContract->getOverviewByRegion();
            return (new PldResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listConsumption(Request $request)
    {
        try {
            $response = $this->pldContract->getListConsumption($request->all());
            return (new PldResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function consumptionByDaily(Request $request)
    {
        try {
            $response = $this->pldContract->getConsumptionByDaily($request->all());
            return (new PldResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function consumptionBySchedule(Request $request)
    {
        try {
            $response = $this->pldContract->getConsumptionBySchedule($request->all());
            return (new PldResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
