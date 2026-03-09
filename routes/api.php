<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\AiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 认证路由
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// 商品路由
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{id}', [ProductController::class, 'show']);
});

// AI 建议路由
Route::prefix('ai')->group(function () {
    Route::post('advice', [AiController::class, 'advice']);
});

// 需要认证的路由
Route::middleware('auth:api')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    // 收藏夹路由
    Route::prefix('user/favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('{productId}', [FavoriteController::class, 'toggle']);
    });
});
