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

            $res = static::checkDate($response);

            dd($res);

            return (new EconomyResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function checkDate($value)
    {
        $start_date = current( $value);

        dd($start_date);


        $y = [];
        foreach ($value as $val) {
            $te = explode('/', $val->mes);
            unset($te[0]);
            $y[] = $te[1];
        }

        $val = collect($y)->unique();

        $start_date = date_create("2021-01-01");
        $end_date = date_create("2022-03-30"); // If you want to include this date, add 1 day

        $interval = DateInterval::createFromDateString('1 months');
        $daterange = new DatePeriod($start_date, $interval, $end_date);

        $date = [];
        foreach ($daterange as $date1) {
            $date[] = $date1->format('m/Y');
        }

        $arr = collect($value)->toArray();

        $i = 0;
        foreach ($date as $dt) {
            if (empty($arr[$i])){
                $arr[] = ['mes' => $dt];
            }
            $i++;
        }

        sort($arr);

        dd($arr);

//        if (!in_array($dt, $arr[$i], true)) {
//            $res[] = $dt;
//        } else {
//            $res[] = $arr[$i];
//        }


        dd($arr);


        dd($arr, $date);
    }

}
