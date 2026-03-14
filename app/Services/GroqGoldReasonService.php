<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqGoldReasonService
{
    public function getReason(string $date): ?string
    {
        $apiKey  = config('services.groq.api_key');
        $apiUrl  = config('services.groq.api_url');
        $model   = config('services.groq.model');

        if (empty($apiKey)) {
            Log::warning('GroqGoldReasonService: GROQ_API_KEY is not configured.');
            return null;
        }

        $prompt = "You are a global gold market analyst. Date analysis: {$date}. "
            . "Your task is to identify the most important global event that affects the price of gold on a given date. "
            . "Analyze based on the key factors that typically influence gold prices, such as: "
            . "Central bank interest rates (Federal Reserve), Inflation, Strength of the US Dollar, "
            . "Geopolitical tensions, Global economic crises, and Gold purchases by central banks. "
            . "The response must be written in concise and professional Bahasa Melayu.";

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])
                ->timeout(30)
                ->post($apiUrl, [
                    'model' => $model,
                    'input' => $prompt,
                ]);

            if (! $response->successful()) {
                Log::error('GroqGoldReasonService: API request failed.', [
                    'date'   => $date,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();

            return $this->parseResponse($json, $date);

        } catch (\Throwable $e) {
            Log::error('GroqGoldReasonService: API request exception.', [
                'date'  => $date,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function parseResponse(?array $json, string $date): ?string
    {
        if (empty($json)) {
            Log::warning('GroqGoldReasonService: Empty JSON response.', ['date' => $date]);
            return null;
        }

        $output = $json['output'] ?? null;

        if (! is_array($output)) {
            Log::warning('GroqGoldReasonService: Missing or invalid "output" array in response.', [
                'date'     => $date,
                'response' => $json,
            ]);
            return null;
        }

        foreach ($output as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (($item['role'] ?? null) !== 'assistant') {
                continue;
            }

            $contentList = $item['content'] ?? [];

            if (! is_array($contentList)) {
                continue;
            }

            foreach ($contentList as $contentItem) {
                if (! is_array($contentItem)) {
                    continue;
                }

                if (($contentItem['type'] ?? null) === 'output_text') {
                    $text = $contentItem['text'] ?? null;

                    if (is_string($text) && trim($text) !== '') {
                        return trim($text);
                    }
                }
            }
        }

        Log::warning('GroqGoldReasonService: Could not locate assistant output_text in response.', [
            'date'     => $date,
            'response' => $json,
        ]);

        return null;
    }
}
