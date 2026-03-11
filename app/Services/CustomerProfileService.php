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

        $user = User::query()->where('meem_id', $data['id'])->first();
        if(!$user) {
            //create new user
            //meem_code si from $data['code']. but make it 8 digits by padding with zeros if less than 8 digits. Add MEEM prefix to the code. For example, if code is 123, then meem_code should be MEEM00000123.
            $meem_code = 'MEEM' . str_pad($data['code'] ?? '', 8, '0', STR_PAD_LEFT);
            User::create([
                'fullname'        => $data['name'] ?? null,
                'email'           => $data['email'] ?? null,
                'phone_number'    => $data['contact_no'] ?? null,
                'meem_code'       => $meem_code ?? null,
                'meem_id'         => $data['id'] ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
                'updated_at'      => now(),
            ]);
             return;
        }else{
            //update existing user
            //print_r($user->toArray()); exit();
            $meem_code = 'MEEM' . str_pad($data['code'] ?? '', 8, '0', STR_PAD_LEFT);
            $user->update([
                'fullname'        => $data['name'] ?? null,
                'email'           => $data['email'] ?? null,
                'phone_number'    => $data['contact_no'] ?? null,
                'meem_code'       => $meem_code ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
                'updated_at'      => now(),
            ]);
             return;
        }
    }
}
