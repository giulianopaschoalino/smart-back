<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJsonMessage;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class NewsController extends Controller
{
    public function send()
    {
        $xmlObject = xmlToObject(config('services.webhook.news'));

        $resource = \json_decode(
            \json_encode($xmlObject->children()),
            true
        );

        $resource = collect($resource['channel']['item'])
            ->transform(
                fn ($item) => Arr::set(
                    $item,
                    'pubDate',
                    Carbon::parse($item['pubDate'])->translatedFormat('D, d F Y H:i:s')
                )
            );

        return ResponseJsonMessage::withData($resource);
    }
}
