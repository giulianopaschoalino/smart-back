<?php

namespace App\Http\Controllers;

use App\Repositories\Faqs\FaqContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            return response()->json($response, 200);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function store(Request $notificationRequest): JsonResponse
    {
        try {
            $response = $this->faq->create($notificationRequest->all());
            return response()->json($response, 200);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $response =  $this->faq->find($id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->faq->update($request->all(), $id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $response = $this->faq->destroy($id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

}