<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Libraries\Far_log;
use App\Libraries\Far_gold;

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
        if($upstream->successful() && ($body['success'] ?? false) && isset($body['data'])) {
            $log_data = $body['data'];
            Far_log::insert_userlog(1, 'api', 'customer', 'profile' ,$log_data);
        }
        if ($upstream->successful() && ($body['success'] ?? false) && isset($body['data'])) {

            $gold_progress_detail = Far_gold::gold_progress_detail($body['data']['gss_balance'] ?? 0);
            $goldPrice = 651; // This should ideally come from a reliable source or config


            $body['data']['gss_progress'] = [
                'balance' => $gold_progress_detail['balance'],
                'threshold' => $gold_progress_detail['threshold'],
                'progress_value' => $gold_progress_detail['progress_value'],
                'progress_percentage' => $gold_progress_detail['progress_percentage'],
                'progress_bar_percentage' => $gold_progress_detail['progress_bar_percentage'],
            ];
            $body['data']['gold_price'] = $goldPrice;
            $body['data']['gss_gold_value'] = 0;
            $body['data']['gss_detail'] = [
                'balance' => round($body['data']['gss_balance'], 4),
                'gold_price' => $goldPrice,
                'gold_value' => round(0, 2),
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
