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
            $a = User::insert([
                'fullname'     => fake()->name(),
                'phone_number' => fake()->numerify('601########'),
                'meem_code'    => 'MEEM' . str_pad(4, 6, '0', STR_PAD_LEFT),
                'email'        => fake()->unique()->safeEmail(),
                'password'     => Hash::make('password'),
                'is_admin'     => false,
                'created_at'   => now(),
                'updated_at'   => now(),
                ]);

            var_dump($a); exit();
             return;
        }else{
            //update existing user
            $user->update([
                'fullname'        => $data['name'] ?? null,
                'email'           => $data['email'] ?? null,
                'phone_number'    => $data['contact_no'] ?? null,
                'meem_code'       => $data['cs_code'] ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
            ]);
             return;
        }
    }
}
