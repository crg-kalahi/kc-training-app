<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Http;
use App\Models\Application;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class OpenAIService
{
    protected $openAIClient;
    protected $geminiApiKey;
    protected $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.key');
    }

    public function generateChatResponse($message)
    {
        return $this->generateGeminiResponse($message);
    }
    

    protected function generateGeminiResponse($message)
    {
        $response = Http::post("{$this->geminiApiUrl}?key={$this->geminiApiKey}", [
            'contents' => [
                ['parts' => [['text' => $message]]],
            ]
        ]);

        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }
    
}
