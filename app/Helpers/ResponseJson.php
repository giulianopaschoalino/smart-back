<?php

namespace App\Helpers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class ResponseJson
{

    public static function message(string $message, $status_code = Response::HTTP_OK): JsonResponse
    {
        return response()->json(compact('message'), $status_code);
    }

    public static function data(mixed $data, $status_code = Response::HTTP_OK): JsonResponse
    {
        return response()->json(compact('data'), $status_code);
    }

    public static function error(mixed $error, int $status_code): JsonResponse
    {
        return response()->json(compact('error'), $status_code);
    }
}
