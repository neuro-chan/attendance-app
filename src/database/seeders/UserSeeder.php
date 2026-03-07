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
                'name'     => 'テストユーザー1',
                'email'    => 'user1@example.com',
                'password' => Hash::make('userpassword'),
                'role'     => UserRole::Staff,
                'email_verified_at'     => 'now'
            ],
            [
                'name'     => 'テストユーザー2',
                'email'    => 'user2@example.com',
                'password' => Hash::make('userpassword'),
                'role'     => UserRole::Staff,
                'email_verified_at'     => 'now'
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
