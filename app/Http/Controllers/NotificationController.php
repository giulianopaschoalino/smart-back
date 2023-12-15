<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJson;
use App\Http\Requests\StoreNotificationRequest;
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
    ) {
    }

    public function index(): JsonResponse
    {
        $response = $this->notification->all();

        return ResponseJson::data($response);
    }

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $response = $this->notification->create($request->validated());
        $response->users()->sync($request->input('users.*.user_id', []));

        return ResponseJson::data($response, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $response =  $this->notification->find($id);

        return ResponseJson::data($response);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $response = $this->notification->update($request->all(), $id);

        return ResponseJson::data($response);
    }

    public function destroy($id): JsonResponse
    {
        $response = $this->notification->destroy($id);

        return ResponseJson::data($response);
    }

    public function notify()
    {
        $response = $this->notification->getNotify();

        // return ResponseJson::data($response);
        return response()->json($response);
    }
}
