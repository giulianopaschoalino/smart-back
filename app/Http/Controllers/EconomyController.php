<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\EconomyResource;
use App\Repositories\Economy\EconomyContractInterface;
use App\Traits\ApiResponse;
use DateInterval;
use DatePeriod;
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

            $res = static::checkDate();

            dd($res);

            return (new EconomyResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function checkDate(){
        $start_date = date_create("2021-04-01");
        $end_date   = date_create("2022-03-01"); // If you want to include this date, add 1 day

        $interval = DateInterval::createFromDateString('1 months');
        $daterange = new DatePeriod($start_date, $interval ,$end_date);

        $res = [];
        foreach($daterange as $date1){

            if (!'02/2022' )

            $res[] = $date1->format('m/Y');
        }

        return $res;
    }

}
