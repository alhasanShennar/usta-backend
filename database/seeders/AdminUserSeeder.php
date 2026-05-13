<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@usta.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('u$ta1234!'),
                'email_verified_at' => now(),
            ],
        );
    }
}
