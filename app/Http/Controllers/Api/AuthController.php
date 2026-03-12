<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(protected AuthService $service) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'required|string',
        ]);

        try {
            $upstream = $this->service->login(
                $request->input('email'),
                $request->input('password'),
                $request->input('device_name'),
            );
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AuthLogin upstream connection failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication service temporarily unavailable.',
            ], 502);
        }

        $body = $upstream->json();

        if ($upstream->successful() && ($body['success'] ?? false) && isset($body['data'])) {
            $meemCode = $body['data']['customer'] ?? null;
            $token    = $body['data']['token'] ?? null;

            if ($meemCode && $token) {
                try {
                    $this->service->syncToken($meemCode, $token);
                } catch (\Throwable $e) {
                    Log::error('AuthLogin token sync failed', ['error' => $e->getMessage()]);
                }
            }
        }

        return response()->json($body, $upstream->status());
    }
}
