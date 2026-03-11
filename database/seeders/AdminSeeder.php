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
            ['email' => 'admin@meem.com.my'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('12345678'),
                'is_admin' => true,
            ]
        );
    }
}
