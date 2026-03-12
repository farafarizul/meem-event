<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function login(string $email, string $password, string $deviceName): Response
    {
        return Http::asForm()
            ->timeout(15)
            ->post('https://meem.com.my/api/v1/auth/login', [
                'email'       => $email,
                'password'    => $password,
                'device_name' => $deviceName,
            ]);
    }

    public function syncToken(string $meemCode, string $token): void
    {
        $user = User::query()->where('meem_code', $meemCode)->first();

        if (! $user) {
            Log::warning('AuthLogin: no local user found for meem_code', ['meem_code' => $meemCode]);
            return;
        }

        $user->update(['token' => $token]);
    }
}
