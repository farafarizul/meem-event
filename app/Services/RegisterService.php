<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class RegisterService
{
    public function register(array $payload): Response
    {
        $baseUrl = config('services.meem.base_url', 'https://meem.com.my/api/v1');

        return Http::asJson()
            ->timeout(15)
            ->post("{$baseUrl}/auth/register", $payload);
    }
}
