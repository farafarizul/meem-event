<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CustomerProfileService
{
    public function fetchProfile(string $token): Response
    {
        //$url = rtrim(config('services.meem.base_url'), '/') . '/customer/profile';

        return Http::withToken($token)
            ->timeout(15)
            ->get('https://meem.com.my/api/v1/customer/profile');
    }

    public function syncUser(array $data): void
    {
        User::updateOrCreate(
            ['meem_id' => $data['id']],
            [
                'fullname'        => $data['name'] ?? null,
                'email'           => $data['email'] ?? null,
                'phone_number'    => $data['contact_no'] ?? null,
                'meem_code'       => $data['cs_code'] ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
            ]
        );
    }
}
