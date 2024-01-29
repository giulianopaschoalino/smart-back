<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJsonMessage;
use App\Http\Requests\StoreNotificationRequest;
use App\Repositories\Notifications\NotificationContractInterface;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    

    public function __construct(
        protected NotificationContractInterface $notification
    ) {
    }

    public function index(): JsonResponse
    {
        $response = $this->notification->all();

        return ResponseJsonMessage::withData($response);
    }

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $response = $this->notification->create($request->validated());
        $response->users()->sync($request->input('users.*.user_id', []));

        return ResponseJsonMessage::withData($response, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $response =  $this->notification->find($id);

        return ResponseJsonMessage::withData($response);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $response = $this->notification->update($request->all(), $id);

        return ResponseJsonMessage::withData($response);
    }

    public function destroy($id): JsonResponse
    {
        $response = $this->notification->destroy($id);

        return ResponseJsonMessage::withData($response);
    }

    public function notify()
    {
        $response = $this->notification->getNotify();

        return ResponseJsonMessage::withData($response);
    }
}
