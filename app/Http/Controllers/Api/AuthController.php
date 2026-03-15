<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\Far_log;
use App\Models\User;
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


            if($meemCode && $token){

                //generate random 20 character string, uppercase only for app_session
                $app_session = strtoupper(substr(bin2hex(random_bytes(10)), 0, 20));
                $device_info = $request->input('device_info', 'unknown');
                //print_r($device_info); exit();
                $body['data']['app_session'] = $app_session;
                $body['data']['device_info'] = $request->input('device_info', 'unknown');
                $body['data']['device_name'] = $request->input('device_name', 'unknown');
                $log_data = $body['data'];
                Far_log::insert_userlog(1, 'api', 'auth', 'login' ,$log_data);
            }


            if ($meemCode && $token) {
                try {
                    $device_name = $request->input('device_name', 'unknown');
                    $this->service->syncToken($meemCode, $token, $device_name);
                } catch (\Throwable $e) {
                    Log::error('AuthLogin token sync failed', ['error' => $e->getMessage()]);
                }
            }
        }

        return response()->json($body, $upstream->status());
    }

    public function logout(Request $request): JsonResponse
    {

        // get Authentication: Bearer token from header
        $authHeader = $request->header('Authorization');
        if(!$authHeader || !str_starts_with($authHeader, 'Bearer ')){
            return response()->json([
                'success' => false,
                'message' => 'Authorization token missing or invalid.',
            ], 401);
        }
        //extract the token from the header
        $token = substr($authHeader, 7);


        $user = User::query()->where('token', $token)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if (empty($user->token)) {
            return response()->json([
                'success' => false,
                'message' => 'No active session token found.',
            ], 422);
        }


        try {
            $upstream = $this->service->logout($user->token);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AuthLogout upstream connection failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication service temporarily unavailable.',
            ], 502);
        }

        $body = $upstream->json();

        if ($upstream->successful() && ($body['success'] ?? false)) {
            try {
                $this->service->syncLogout($user->meem_code);

                $body['app_session'] = $request->input('app_session', 'unknown');

                $log_data = $body;



                $log_data['meem_code'] = $user->meem_code;



                Far_log::insert_userlog(1, 'api', 'auth', 'logout' ,$log_data);

            } catch (\Throwable $e) {
                Log::error('AuthLogout status sync failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json($body, $upstream->status());
    }
}
