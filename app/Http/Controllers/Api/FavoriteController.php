<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function toggle($productId)
    {
        $user = auth()->user();

        if ($user->favorites()->where('product_id', $productId)->exists()) {
            $user->favorites()->detach($productId);
            $isFavorite = false;
        } else {
            $user->favorites()->attach($productId);
            $isFavorite = true;
        }

        return response()->json([
            'isFavorite' => $isFavorite,
            'message' => $isFavorite ? '已添加到收藏' : '已取消收藏',
        ]);
    }

    public function index()
    {
        $user = auth()->user();
        $favorites = $user->favorites()
            ->with(['category', 'wholesalePrices', 'history'])
            ->get();

        return ProductResource::collection($favorites);
    }
}
