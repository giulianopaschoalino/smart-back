<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Helpers\ResponseJson;
use App\Traits\ApiResponse;
use App\Repositories\Pld\PldContractInterface;

use Illuminate\Http\Request;

class PldController extends Controller
{

    use ApiResponse;

    public function __construct(
        protected PldContractInterface $pldContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->pldContract->search($request->all());
        $response = Helpers::orderByDate($response, 'm/Y', 'mes_ref');

        return ResponseJson::data($response);
    }

    public function overviewByRegion()
    {
        $response = $this->pldContract->getOverviewByRegion();

        return ResponseJson::data($response);
    }

    public function listConsumption(Request $request)
    {
        $response = $this->pldContract->getListConsumption($request->all());

        // return ResponseJson::data($response);
        return response()->json($response);
    }

    public function consumptionByDaily(Request $request)
    {
        $response = $this->pldContract->getConsumptionByDaily($request->all());

        return ResponseJson::data($response);
    }

    public function consumptionBySchedule(Request $request)
    {
        $response = $this->pldContract->getConsumptionBySchedule($request->all());

        return ResponseJson::data($response);
    }
}
