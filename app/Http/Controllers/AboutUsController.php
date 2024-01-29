<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJsonMessage;
use App\Http\Requests\StoreAboutUsRequest;
use App\Repositories\AboutUs\AboutUsContractInterface;

class AboutUsController extends Controller
{
    

    public function __construct(
        protected AboutUsContractInterface $aboutUsContract
    ) {
    }

    public function index()
    {
        $response = $this->aboutUsContract->all();

        return ResponseJsonMessage::withData($response);
    }


    public function store(StoreAboutUsRequest $aboutUsRequest)
    {

        $about = $this->aboutUsContract->max('id');

        if ($about !== null) {
            $response = $this->aboutUsContract->update($aboutUsRequest->validated(), $about);
        } else {
            $response = $this->aboutUsContract->create($aboutUsRequest->validated());
        }

        return ResponseJsonMessage::withData($response);
    }
}
