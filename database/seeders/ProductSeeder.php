<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\WholesalePrice;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // 创建分类
        $categories = [
            ['name' => '蔬菜', 'slug' => 'vegetables'],
            ['name' => '水果', 'slug' => 'fruits'],
            ['name' => '肉类', 'slug' => 'meat'],
            ['name' => '水产', 'slug' => 'seafood'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(['slug' => $categoryData['slug']], $categoryData);
        }

        // 创建商品数据
        $products = [
            [
                'category' => 'vegetables',
                'name' => '西红柿',
                'unit' => '斤',
                'current_price' => 4.50,
                'prev_price' => 4.20,
                'market_status' => 'up',
                'image_url' => 'https://example.com/tomato.jpg',
                'tags' => ['新鲜', '当季'],
            ],
            [
                'category' => 'vegetables',
                'name' => '黄瓜',
                'unit' => '斤',
                'current_price' => 3.80,
                'prev_price' => 4.00,
                'market_status' => 'down',
                'image_url' => 'https://example.com/cucumber.jpg',
                'tags' => ['新鲜'],
            ],
            [
                'category' => 'fruits',
                'name' => '苹果',
                'unit' => '斤',
                'current_price' => 6.50,
                'prev_price' => 6.50,
                'market_status' => 'stable',
                'image_url' => 'https://example.com/apple.jpg',
                'tags' => ['进口', '优质'],
            ],
            [
                'category' => 'fruits',
                'name' => '香蕉',
                'unit' => '斤',
                'current_price' => 5.20,
                'prev_price' => 5.80,
                'market_status' => 'down',
                'image_url' => 'https://example.com/banana.jpg',
                'tags' => ['热带水果'],
            ],
            [
                'category' => 'meat',
                'name' => '猪肉',
                'unit' => '斤',
                'current_price' => 18.50,
                'prev_price' => 17.80,
                'market_status' => 'up',
                'image_url' => 'https://example.com/pork.jpg',
                'tags' => ['新鲜', '精选'],
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('slug', $productData['category'])->first();

            $product = Product::firstOrCreate(
                ['name' => $productData['name']],
                [
                    'category_id' => $category->id,
                    'unit' => $productData['unit'],
                    'current_price' => $productData['current_price'],
                    'prev_price' => $productData['prev_price'],
                    'market_status' => $productData['market_status'],
                    'image_url' => $productData['image_url'],
                    'tags' => $productData['tags'],
                ]
            );

            // 创建价格历史（最近7天）
            for ($i = 6; $i >= 0; $i--) {
                $basePrice = $productData['current_price'];
                $variance = rand(-50, 50) / 100; // -0.5 到 +0.5 的随机波动

                PriceHistory::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'recorded_at' => Carbon::now()->subDays($i)->format('Y-m-d'),
                    ],
                    [
                        'price' => round($basePrice + $variance, 2),
                    ]
                );
            }

            // 创建批发市场价格
            $markets = ['新发地市场', '八里桥市场', '顺义石门市场'];
            foreach ($markets as $market) {
                $wholesalePrice = $productData['current_price'] * 0.7; // 批发价约为零售价的70%
                $variance = rand(-30, 30) / 100;

                WholesalePrice::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'market_name' => $market,
                    ],
                    [
                        'price' => round($wholesalePrice + $variance, 2),
                    ]
                );
            }
        }
    }
}
