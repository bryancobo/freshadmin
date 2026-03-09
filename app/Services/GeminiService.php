<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeminiService
{
    protected $client;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);

        $this->apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    }

    public function getAdvice($query, $context, $isElderly = false)
    {
        $systemPrompt = $isElderly
            ? "你是一位专业的生鲜采购顾问。请用简单易懂的语言，为老年人提供购买建议。语气要亲切、耐心，避免使用复杂的专业术语。"
            : "你是一位专业的生鲜采购顾问。请根据当前市场价格信息，为用户提供专业的购买建议。";

        $prompt = "{$systemPrompt}\n\n{$context}\n\n用户问题：{$query}\n\n请提供详细的购买建议，包括价格分析、购买时机、性价比评估等。请使用 Markdown 格式输出。";

        try {
            $response = $this->client->post($this->apiUrl . '?key=' . $this->apiKey, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }

            throw new \Exception('Gemini API 返回格式异常');

        } catch (GuzzleException $e) {
            throw new \Exception('调用 Gemini API 失败: ' . $e->getMessage());
        }
    }
}
