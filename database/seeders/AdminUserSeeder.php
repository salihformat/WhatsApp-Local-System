<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // إنشاء مستخدم أدمن
        User::updateOrCreate(
            ['email' => 'admin@rasayily.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );

        // إنشاء مستخدم عادي
        User::updateOrCreate(
            ['email' => 'user@rasayily.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('user123'),
                'is_admin' => false,
            ]
        );
    }
}
