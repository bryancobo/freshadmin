<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // 微信小程序配置
        $appId = env('WECHAT_MINI_APP_ID');
        $appSecret = env('WECHAT_MINI_APP_SECRET');

        // 调用微信接口获取 openid
        $response = Http::get('https://api.weixin.qq.com/sns/jscode2session', [
            'appid' => $appId,
            'secret' => $appSecret,
            'js_code' => $request->code,
            'grant_type' => 'authorization_code',
        ]);

        $data = $response->json();

        if (isset($data['errcode']) && $data['errcode'] !== 0) {
            return response()->json([
                'message' => '微信登录失败',
                'error' => $data['errmsg'] ?? 'Unknown error',
            ], 400);
        }

        $openid = $data['openid'];

        // 查找或创建用户
        $user = User::firstOrCreate(
            ['openid' => $openid],
            [
                'nickname' => $request->nickname ?? '用户' . substr($openid, -6),
                'avatar_url' => $request->avatar_url,
                'member_level' => 'basic',
            ]
        );

        // 生成 JWT Token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nickname' => $user->nickname,
                'avatarUrl' => $user->avatar_url,
                'memberLevel' => $user->member_level,
            ],
        ]);
    }
}
