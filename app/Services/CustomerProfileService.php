<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Hash;
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

        //check if user exists by meem_id, if exists update the record, if not create new record.
        if (empty($data['id'])) {
            return;
        }

        $user = User::query()->where('meem_code', $data['cs_code'])->first();
        if(!$user) {
            //create new user

            User::create([
                'fullname'        => $data['name'] ?? null,
                'email'           => $data['email'] ?? null,
                'phone_number'    => $data['contact_no'] ?? null,
                'meem_code'       => $data['cs_code'] ?? null,
                'meem_id'         => $data['id'] ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
                'updated_at'      => now(),
            ]);
             return;
        }else{
            //update existing user

            $user->update([
                'fullname'        => $data['name'] ?? null,
                'email'           => $data['email'] ?? null,
                'phone_number'    => $data['contact_no'] ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
                'updated_at'      => now(),
            ]);
             return;
        }
    }
}
