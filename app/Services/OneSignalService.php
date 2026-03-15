<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class OneSignalService
{
    private string $appId;
    private string $restApiKey;
    private string $apiUrl = 'https://onesignal.com/api/v1/notifications';

    public function __construct()
    {
        $this->appId      = config('services.onesignal.app_id');
        $this->restApiKey = config('services.onesignal.rest_api_key');
    }

    /**
     * Build the base payload shared by all send modes.
     */
    private function buildBasePayload(
        string $title,
        string $message,
        ?string $imageUrl,
        ?string $additionalData1,
        ?string $additionalData2,
        ?string $additionalData3
    ): array {
        $payload = [
            'app_id'   => $this->appId,
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
        ];

        if (!empty($imageUrl)) {
            $payload['big_picture'] = $imageUrl;
            $payload['ios_attachments'] = ['image' => $imageUrl];
        }

        $data = [];
        if (!empty($additionalData1)) {
            $data['field1'] = $additionalData1;
        }
        if (!empty($additionalData2)) {
            $data['field2'] = $additionalData2;
        }
        if (!empty($additionalData3)) {
            $data['field3'] = $additionalData3;
        }
        if (!empty($data)) {
            $payload['data'] = $data;
        }

        return $payload;
    }

    /**
     * Send push notification to all subscribed users.
     *
     * @return array{success: bool, notification_id: string|null, total_count: int, raw_response: string, error: string|null, payload: array}
     */
    public function sendToAll(
        string $title,
        string $message,
        ?string $imageUrl = null,
        ?string $additionalData1 = null,
        ?string $additionalData2 = null,
        ?string $additionalData3 = null
    ): array {
        $payload = $this->buildBasePayload($title, $message, $imageUrl, $additionalData1, $additionalData2, $additionalData3);
        $payload['included_segments'] = ['All'];

        return $this->dispatch($payload);
    }

    /**
     * Send push notification to selected users by their meem_code (external_id).
     *
     * @param  string[] $meemCodes
     * @return array{success: bool, notification_id: string|null, total_count: int, raw_response: string, error: string|null, payload: array}
     */
    public function sendToSelected(
        array $meemCodes,
        string $title,
        string $message,
        ?string $imageUrl = null,
        ?string $additionalData1 = null,
        ?string $additionalData2 = null,
        ?string $additionalData3 = null
    ): array {
        if (empty($meemCodes)) {
            return [
                'success'         => false,
                'notification_id' => null,
                'total_count'     => 0,
                'raw_response'    => '',
                'error'           => 'No recipients selected.',
                'payload'         => [],
            ];
        }

        $payload = $this->buildBasePayload($title, $message, $imageUrl, $additionalData1, $additionalData2, $additionalData3);
        $payload['include_aliases']   = ['external_id' => array_values($meemCodes)];
        $payload['target_channel']    = 'push';

        return $this->dispatch($payload);
    }

    /**
     * Dispatch the payload to OneSignal via cURL and return a standardised result.
     *
     * @return array{success: bool, notification_id: string|null, total_count: int, raw_response: string, error: string|null, payload: array}
     */
    private function dispatch(array $payload): array
    {
        $jsonPayload = json_encode($payload);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: key ' . $this->restApiKey,
            ],
        ]);

        $rawResponse = curl_exec($ch);
        $curlError   = curl_error($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // cURL-level failure
        if ($rawResponse === false || !empty($curlError)) {
            $error = 'cURL error: ' . ($curlError ?: 'Unknown cURL error');
            Log::error('OneSignalService cURL error', ['error' => $error, 'payload' => $payload]);
            return [
                'success'         => false,
                'notification_id' => null,
                'total_count'     => 0,
                'raw_response'    => '',
                'error'           => $error,
                'payload'         => $payload,
            ];
        }

        // Empty response body
        if (empty($rawResponse)) {
            $error = 'Empty response received from OneSignal.';
            Log::error('OneSignalService empty response', ['http_code' => $httpCode, 'payload' => $payload]);
            return [
                'success'         => false,
                'notification_id' => null,
                'total_count'     => 0,
                'raw_response'    => '',
                'error'           => $error,
                'payload'         => $payload,
            ];
        }

        // Attempt JSON decode
        $decoded = json_decode($rawResponse, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'Invalid JSON response from OneSignal: ' . json_last_error_msg();
            Log::error('OneSignalService JSON decode error', ['raw' => $rawResponse, 'payload' => $payload]);
            return [
                'success'         => false,
                'notification_id' => null,
                'total_count'     => 0,
                'raw_response'    => $rawResponse,
                'error'           => $error,
                'payload'         => $payload,
            ];
        }

        // OneSignal API-level error
        if (isset($decoded['errors']) || $httpCode >= 400) {
            $errorMessages = [];
            if (!empty($decoded['errors'])) {
                $errorMessages = is_array($decoded['errors'])
                    ? $decoded['errors']
                    : [$decoded['errors']];
            }
            $error = 'OneSignal API error (HTTP ' . $httpCode . '): ' . implode('; ', $errorMessages);
            Log::error('OneSignalService API error', ['http_code' => $httpCode, 'response' => $decoded, 'payload' => $payload]);
            return [
                'success'         => false,
                'notification_id' => $decoded['id'] ?? null,
                'total_count'     => $decoded['recipients'] ?? 0,
                'raw_response'    => $rawResponse,
                'error'           => $error,
                'payload'         => $payload,
            ];
        }

        return [
            'success'         => true,
            'notification_id' => $decoded['id'] ?? null,
            'total_count'     => $decoded['recipients'] ?? 0,
            'raw_response'    => $rawResponse,
            'error'           => null,
            'payload'         => $payload,
        ];
    }

    public function getAppId(): string
    {
        return $this->appId;
    }
}
