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

    public function syncToken(string $meemCode, string $token, string $device_name): void
    {
        $user = User::query()->where('meem_code', $meemCode)->first();

        if (! $user) {
            $user = User::create([
                'fullname'        => "-",
                'email'           => "-",
                'phone_number'    => "-",
                'meem_code'       => $meemCode,
                'meem_id'         => time(), // Use current timestamp as a placeholder meem_id
                'profile_picture' => null,
                'device_name'      => $device_name,
                'updated_at'      => now(),
            ]);

            Log::warning('AuthLogin: no local user found for meem_code', ['meem_code' => $meemCode]);
        }

        print_r($device_name."asdsadsasad"); exit();

        $user->update(['token' => $token, 'apps_login_status' => 'logged_in', 'device_name' => 'asdasdasdasdasdasdsad']);
    }

    public function logout(string $token): Response
    {

        return Http::withToken($token)
            ->timeout(15)
            ->get('https://meem.com.my/api/v1/auth/logout');
    }

    public function syncLogout(string $meemCode): void
    {
        User::query()->where('meem_code', $meemCode)->update(['apps_login_status' => 'logged_out']);
    }
}
