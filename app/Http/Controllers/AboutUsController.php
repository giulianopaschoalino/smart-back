<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAboutUsRequest;
use App\Http\Resources\AboutUsResource;
use App\Models\AboutUs;
use App\Repositories\AboutUs\AboutUsContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;

class AboutUsController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AboutUsContractInterface $aboutUsContract
    ){}

    public function index()
    {
        try {
            $response = $this->aboutUsContract->all();
            return (new AboutUsResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function store(StoreAboutUsRequest $aboutUsRequest)
    {
        try {

            $about = $this->aboutUsContract->max('id');

            if ($about !== null)
            {
                $response = $this->aboutUsContract->update($aboutUsRequest->validated(), $about);
            } else {
                $response = $this->aboutUsContract->create($aboutUsRequest->validated());
            }
            return (new AboutUsResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_ACCEPTED);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}