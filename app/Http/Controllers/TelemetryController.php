<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TelemetryResource;
use App\Repositories\Med5min\Med5minContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelemetryController extends Controller
{

    use ApiResponse;

    public function __construct(
        protected Med5minContractInterface $med5minContract
    ){}

    public function index()
    {
        //
    }

    public function powerFactor(Request $request)
    {
        try {
            $response = $this->med5minContract->getPowerFactor($request->all());
            return (new TelemetryResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function demand(Request $request)
    {
        try {
            $response = $this->med5minContract->getDemand($request->all());
            return (new TelemetryResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function discretization(Request $request)
    {
        try {
            $response = $this->med5minContract->getDiscretization($request->all());
            return (new TelemetryResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}