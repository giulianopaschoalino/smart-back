<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Helpers\ResponseJsonMessage;
use App\Repositories\Faqs\FaqContractInterface;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FaqController extends Controller
{
    

    public function __construct(
        protected FaqContractInterface $faq
    ) {
    }

    public function index(): JsonResponse
    {
        $response = $this->faq->all();

        return ResponseJsonMessage::withData($response);
    }

    public function store(Request $notificationRequest): JsonResponse
    {
        $response = $this->faq->create($notificationRequest->all());

        return ResponseJsonMessage::withData($response, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $response =  $this->faq->find($id);

        return ResponseJsonMessage::withData($response);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $response = $this->faq->update($request->all(), $id);

        return ResponseJsonMessage::withData($response);
    }

    public function destroy($id): JsonResponse
    {
        $response = $this->faq->destroy($id);

        return ResponseJsonMessage::withData($response);
    }
}
