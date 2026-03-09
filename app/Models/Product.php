<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'unit',
        'current_price',
        'prev_price',
        'market_status',
        'image_url',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'current_price' => 'decimal:2',
        'prev_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function history()
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function wholesalePrices()
    {
        return $this->hasMany(WholesalePrice::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }
}
