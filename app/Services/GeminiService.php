<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent';
    }

    public function analizarTexto(string $texto): array
    {
        $response = Http::timeout(120)->post(
            $this->baseUrl . '?key=' . $this->apiKey,
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $texto]
                        ]
                    ]
                ]
            ]
        );

        return $response->json();
    }

    public function analizarImagen(string $base64Data, string $mimeType, string $prompt): array
    {
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $base64Data,
                            ]
                        ],
                        ['text' => $prompt]
                    ]
                ]
            ],
          'generationConfig' => [ 
            'temperature' => 0.1, 
            'topP' => 1,
             'topK' => 32, 
             'responseMimeType' => 'application/json', 
             ],
        ];

        Log::debug('Gemini Vision API request', [
            'mimeType' => $mimeType,
            'dataLength' => strlen($base64Data),
        ]);

        $response = Http::timeout(120)->post(
            $this->baseUrl . '?key=' . $this->apiKey,
            $payload
        );

        $json = $response->json();

        Log::debug('Gemini Vision API response status', [
            'httpStatus' => $response->status(),
            'hasCandidates' => isset($json['candidates']),
        ]);

        return $json;
    }

    public function extractTextFromResponse(array $response): ?string
    {
        return $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
