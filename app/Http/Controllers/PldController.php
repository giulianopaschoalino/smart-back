<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Helpers\ResponseJsonMessage;

use App\Repositories\Pld\PldContractInterface;

use Illuminate\Http\Request;

class PldController extends Controller
{

    

    public function __construct(
        protected PldContractInterface $pldContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->pldContract->search($request->all());
        $response = Helpers::orderByDate($response, 'm/Y', 'mes_ref');

        return ResponseJsonMessage::withData($response);
    }

    public function overviewByRegion()
    {
        $response = $this->pldContract->getOverviewByRegion();

        return ResponseJsonMessage::withData($response);
    }

    public function listConsumption(Request $request)
    {
        $response = $this->pldContract->getListConsumption($request->all());

        return ResponseJsonMessage::withData($response);
    }

    public function consumptionByDaily(Request $request)
    {
        $response = $this->pldContract->getConsumptionByDaily($request->all());

        return ResponseJsonMessage::withData($response);
    }

    public function consumptionBySchedule(Request $request)
    {
        $response = $this->pldContract->getConsumptionBySchedule($request->all());

        return ResponseJsonMessage::withData($response);
    }
}
