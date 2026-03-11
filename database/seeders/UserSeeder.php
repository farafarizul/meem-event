<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 30 dummy users with sequential meem_code starting from MEEM000001
        $users = [];
        for ($i = 1; $i <= 30; $i++) {
            $users[] = [
                'fullname'     => fake()->name(),
                'phone_number' => fake()->numerify('601########'),
                'meem_code'    => 'MEEM' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'email'        => fake()->unique()->safeEmail(),
                'password'     => Hash::make('password'),
                'is_admin'     => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        User::insert($users);
    }
}
