<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJson;
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
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->med5minContract->search($request->all());

        return ResponseJson::data($response);
    }

    public function discretization(Request $request)
    {
        $response = $this->med5minContract->getDiscretization($request->all(), $request->getPathInfo());

        return ResponseJson::data($response);
    }

    public function download(Request $request)
    {
        $response = $this->med5minContract->getDiscretization($request->all(), $request->getPathInfo());

        return ResponseJson::data($response);
    }
}
