<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\FaqResource;
use App\Repositories\Faqs\FaqContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FaqController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected FaqContractInterface $faq
    ){}

    public function index(): JsonResponse
    {
        try {
            $response = $this->faq->all();
            return (new FaqResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $notificationRequest): JsonResponse
    {
        try {
            $response = $this->faq->create($notificationRequest->all());
            return (new FaqResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $response =  $this->faq->find($id);
            return (new FaqResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->faq->update($request->all(), $id);
            return (new FaqResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_ACCEPTED);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $response = $this->faq->destroy($id);
            return response()->json($response, Response::HTTP_NO_CONTENT);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}