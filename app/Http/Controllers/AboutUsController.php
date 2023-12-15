<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJson;
use App\Http\Requests\StoreAboutUsRequest;
use App\Http\Resources\AboutUsResource;
use App\Repositories\AboutUs\AboutUsContractInterface;
use App\Traits\ApiResponse;

use Illuminate\Http\Response;

class AboutUsController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AboutUsContractInterface $aboutUsContract
    ) {
    }

    public function index()
    {
        $response = $this->aboutUsContract->all();

        return ResponseJson::data($response);
    }


    public function store(StoreAboutUsRequest $aboutUsRequest)
    {

        $about = $this->aboutUsContract->max('id');

        if ($about !== null) {
            $response = $this->aboutUsContract->update($aboutUsRequest->validated(), $about);
        } else {
            $response = $this->aboutUsContract->create($aboutUsRequest->validated());
        }

        return ResponseJson::data($response);
    }
}
