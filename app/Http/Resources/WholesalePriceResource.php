<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WholesalePriceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'marketName' => $this->market_name,
            'price' => (float) $this->price,
        ];
    }
}
