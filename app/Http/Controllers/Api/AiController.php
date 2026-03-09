<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use App\Models\Product;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function advice(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'isElderly' => 'boolean',
        ]);

        $query = $request->query;
        $isElderly = $request->isElderly ?? false;

        // 从数据库获取相关商品价格信息作为上下文
        $products = Product::with(['wholesalePrices', 'history'])
            ->where('name', 'like', '%' . $query . '%')
            ->limit(5)
            ->get();

        // 构建上下文
        $context = $this->buildContext($products);

        // 调用 Gemini API
        try {
            $advice = $this->geminiService->getAdvice($query, $context, $isElderly);

            return response()->json([
                'advice' => $advice,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'AI 服务暂时不可用',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildContext($products)
    {
        if ($products->isEmpty()) {
            return '暂无相关商品价格信息。';
        }

        $context = "当前市场价格信息：\n\n";

        foreach ($products as $product) {
            $context .= "商品：{$product->name}\n";
            $context .= "当前价格：¥{$product->current_price}/{$product->unit}\n";
            $context .= "市场趋势：{$product->market_status}\n";

            if ($product->wholesalePrices->isNotEmpty()) {
                $context .= "批发市场价格：\n";
                foreach ($product->wholesalePrices as $wp) {
                    $context .= "  - {$wp->market_name}: ¥{$wp->price}\n";
                }
            }

            $context .= "\n";
        }

        return $context;
    }
}
