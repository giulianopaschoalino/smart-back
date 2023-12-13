<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Helpers\ResponseJson;
use App\Repositories\Faqs\FaqContractInterface;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FaqController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected FaqContractInterface $faq
    ) {
    }

    public function index(): JsonResponse
    {
        $response = $this->faq->all();

        return ResponseJson::data($response);
    }

    public function store(Request $notificationRequest): JsonResponse
    {
        $response = $this->faq->create($notificationRequest->all());

        return ResponseJson::data($response, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $response =  $this->faq->find($id);

        return ResponseJson::data($response);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $response = $this->faq->update($request->all(), $id);

        return ResponseJson::data($response);
    }

    public function destroy($id): JsonResponse
    {
        $response = $this->faq->destroy($id);

        return ResponseJson::data($response);
    }
}
