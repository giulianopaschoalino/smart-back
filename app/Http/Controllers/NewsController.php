<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use Illuminate\Http\Response;

class NewsController extends Controller
{
    public function send()
    {
        try {
            $xmlObject = xmlToObject(config('services.webhook.news'));
            $resource = @json_decode(@json_encode($xmlObject->children()), true);
            return (new NewsResource($resource))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}