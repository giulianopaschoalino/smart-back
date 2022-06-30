<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class NewsController extends Controller
{
    public function send()
    {
        try {
            $xmlObject = xmlToObject(config('services.webhook.news'));
            $resource = @json_decode(@json_encode($xmlObject->children()), true);
            $resource = collect($resource['channel']['item'])
                ->transform(fn($item)
                => Arr::set($item, 'pubDate', Carbon::parse($item['pubDate'])->translatedFormat('D, d F Y H:i:s')));
            return (new NewsResource($resource))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}