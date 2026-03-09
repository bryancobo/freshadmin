<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'wholesalePrices']);

        // 按分类筛选
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 模糊搜索
        if ($request->has('q') && $request->q) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // 加载最近7天的价格历史
        $query->with(['history' => function ($q) {
            $q->where('recorded_at', '>=', Carbon::now()->subDays(7))
              ->orderBy('recorded_at', 'asc');
        }]);

        $products = $query->paginate(20);

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = Product::with([
            'category',
            'wholesalePrices',
            'history' => function ($q) {
                $q->where('recorded_at', '>=', Carbon::now()->subDays(7))
                  ->orderBy('recorded_at', 'asc');
            }
        ])->findOrFail($id);

        return new ProductResource($product);
    }
}
