<?php

declare(strict_types=1);

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
            return (new NotificationResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $response =  $this->notification->find($id);
            return (new NotificationResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->notification->update($request->all(), $id);
            return (new NotificationResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_ACCEPTED);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $response = $this->notification->destroy($id);
            return response()->json($response, Response::HTTP_NO_CONTENT);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function notify()
    {
        try {
            $response = $this->notification->getNotify();
            return response()->json($response, Response::HTTP_OK);
        }catch (\Exception $ex){
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}