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
