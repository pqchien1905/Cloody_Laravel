<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo tài khoản Admin
        User::updateOrCreate(
            ['email' => 'admin@cloudbox.com'],
            [
                'name' => 'Admin CloudBox',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Tạo tài khoản User thường
        User::updateOrCreate(
            ['email' => 'user@cloudbox.com'],
            [
                'name' => 'User Demo',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Tạo thêm một vài user demo
        User::updateOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
                'phone' => '0123456789',
                'address' => 'Hanoi, Vietnam',
                'bio' => 'Software Developer',
            ]
        );
    }
}
