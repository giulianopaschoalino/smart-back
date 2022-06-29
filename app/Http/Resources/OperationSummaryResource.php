<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class OperationSummaryResource extends JsonResource
{

    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return parent::toArray($request);
    }
}
