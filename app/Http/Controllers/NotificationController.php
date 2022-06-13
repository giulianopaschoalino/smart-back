<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Repositories\Notifications\NotificationContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected NotificationContractInterface $notification
    ){}

    public function index(): JsonResponse
    {
        try {
            $response = $this->notification->all();
            return response()->json($response, 200);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        try {
            $response = $this->notification->create($request->validated());
            $response->users()->sync($request->input('users.*.user_id', []));

            return (new NotificationResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $response =  $this->notification->find($id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->notification->update($request->all(), $id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $response = $this->notification->destroy($id);
            return response()->json($response, 200);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), 404);
        }
    }
}