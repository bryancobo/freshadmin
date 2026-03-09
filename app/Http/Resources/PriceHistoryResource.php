<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceHistoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'date' => $this->recorded_at->format('Y-m-d'),
            'price' => (float) $this->price,
        ];
    }
}
