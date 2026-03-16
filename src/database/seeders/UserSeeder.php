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
                'name' => 'test01',
                'email' => 'testuser1@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => 'test02',
                'email' => 'testuser2@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '西 伶奈',
                'email' => 'reina.n@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '山田 太郎',
                'email' => 'tato.y@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '増田 一世',
                'email' => 'issei.y@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '山本 敬吉',
                'email' => 'keikichi.y@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '秋田 朋美',
                'email' => 'tomomi.a@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
            [
                'name' => '中西 教夫',
                'email' => 'norio.n@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'email_verified_at' => 'now',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
