<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit' => $this->unit,
            'currentPrice' => (float) $this->current_price,
            'prevPrice' => $this->prev_price ? (float) $this->prev_price : null,
            'marketStatus' => $this->market_status,
            'imageUrl' => $this->image_url,
            'tags' => $this->tags ?? [],
            'category' => $this->category ? $this->category->name : null,
            'wholesalePrices' => WholesalePriceResource::collection($this->whenLoaded('wholesalePrices')),
            'priceHistory' => PriceHistoryResource::collection($this->whenLoaded('history')),
            'isFavorite' => $this->when(
                auth()->check(),
                function () {
                    return $this->favoritedBy()->where('user_id', auth()->id())->exists();
                }
            ),
        ];
    }
}
