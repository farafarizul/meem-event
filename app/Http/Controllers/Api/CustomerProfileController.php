<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerProfileController extends Controller
{
    public function __construct(protected CustomerProfileService $service) {}

    public function show(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization');



        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token is required.',
            ], 401);
        }

        $token = substr($authHeader, 7);

        try {
            $upstream = $this->service->fetchProfile($token);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to reach upstream service.',
            ], 502);
        }

        $body = $upstream->json();
        //modify $body to add below json.
        /*
         "gss_progress": {
            "balance": 0.0094,
            "threshold": 0.01,
            "progress_value": 0.0006,
            "progress_percentage": 94,
            "progress_bar_percentage": 0.94
        },
        "gold_price": 651,
        "gss_gold_value": 6.12,
        "gss_detail": {
            "balance": 0.0094,
            "gold_price": 651,
            "gold_value": 6.12
        }
         */
        if ($upstream->successful() && ($body['success'] ?? false) && isset($body['data'])) {
            $balance = $body['data']['gss_balance'] ?? 0;
            $threshold = 0.01;
            $progressValue = max(0, min($balance, $threshold));
            $progressPercentage = ($threshold > 0) ? ($progressValue / $threshold) * 100 : 0;
            $progressBarPercentage = ($threshold > 0) ? ($progressValue / $threshold) : 0;
            $goldPrice = 651; // This should ideally come from a reliable source or config
            $gssGoldValue = $balance * $goldPrice;

            $body['data']['gss_progress'] = [
                'balance' => $balance,
                'threshold' => $threshold,
                'progress_value' => round($progressValue, 4),
                'progress_percentage' => round($progressPercentage, 2),
                'progress_bar_percentage' => round($progressBarPercentage, 4),
            ];
            $body['data']['gold_price'] = $goldPrice;
            $body['data']['gss_gold_value'] = round($gssGoldValue, 2);
            $body['data']['gss_detail'] = [
                'balance' => round($balance, 4),
                'gold_price' => $goldPrice,
                'gold_value' => round($gssGoldValue, 2),
            ];
        }

        if ($upstream->successful() && ($body['success'] ?? false) && isset($body['data'])) {
            try {
                $this->service->syncUser($body['data']);
            } catch (\Throwable $e) {
                Log::error('CustomerProfile sync failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json($body, $upstream->status());
    }
}
