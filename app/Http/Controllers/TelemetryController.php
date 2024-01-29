<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJsonMessage;
use App\Http\Resources\TelemetryResource;
use App\Repositories\Med5min\Med5minContractInterface;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelemetryController extends Controller
{

    

    public function __construct(
        protected Med5minContractInterface $med5minContract
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->med5minContract->search($request->all());

        return ResponseJsonMessage::withData($response);
    }

    public function discretization(Request $request)
    {
        $response = $this->med5minContract->getDiscretization($request->all(), $request->getPathInfo());

        return ResponseJsonMessage::withData($response);
    }

    public function download(Request $request)
    {
        $response = $this->med5minContract->getDiscretization($request->all(), $request->getPathInfo());

        return ResponseJsonMessage::withData($response);
    }
}
