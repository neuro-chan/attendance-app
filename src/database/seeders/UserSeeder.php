<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => '管理者',
                'email' => 'admin@example.com',
                'password' => Hash::make('adminpassword'),
                'role' => UserRole::Admin,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '西 伶奈',
                'email' => 'user1@example.com',
                'password' => Hash::make('userpassword'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '山田 太郎',
                'email' => 'user2@example.com',
                'password' => Hash::make('userpassword'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
