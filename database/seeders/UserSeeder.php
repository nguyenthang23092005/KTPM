<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin account
        User::create([
            'user_id' => 'AD_001',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'gender' => 'Nam',
            'phone' => '0903000000',
            'birth_date' => '1990-01-15',
            'address' => 'Hà Nội, Việt Nam',
        ]);

        // Test customer accounts
        User::create([
            'user_id' => 'KH_001',
            'name' => 'Nguyễn Văn A',
            'email' => 'customer1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'gender' => 'Nam',
            'phone' => '0901111111',
            'birth_date' => '1995-05-20',
            'address' => 'TP. Hồ Chí Minh, Việt Nam',
        ]);

        User::create([
            'user_id' => 'KH_002',
            'name' => 'Trần Thị B',
            'email' => 'customer2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'gender' => 'Nữ',
            'phone' => '0902222222',
            'birth_date' => '1998-08-30',
            'address' => 'Đà Nẵng, Việt Nam',
        ]);

        User::create([
            'user_id' => 'KH_003',
            'name' => 'Lê Minh C',
            'email' => 'customer3@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'gender' => 'Nam',
            'phone' => '0903333333',
            'birth_date' => '1996-03-12',
            'address' => 'Cần Thơ, Việt Nam',
        ]);
    }
}
