<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => '管理者',
            'email'    => 'admin@example.com',
            'password' => Hash::make('adminpassword'),
            'role'     => UserRole::Admin,
        ]);
    }
}
