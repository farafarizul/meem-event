<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['meem_code' => 'MEEM000000'],
            [
                'fullname'     => 'Admin Meem',
                'phone_number' => '60123456789',
                'meem_code'    => 'MEEM000000',
                'email'        => 'admin@meem.com.my',
                'password'     => Hash::make('12345678'),
                'is_admin'     => true,
            ]
        );
    }
}
