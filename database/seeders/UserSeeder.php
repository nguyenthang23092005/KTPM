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
            'name' => 'Thang',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'gender' => 'Nam',
            'phone' => '0903000000',
            'birth_date' => '1990-01-15',
            'address' => 'Hà Nội, Việt Nam',
        ]);

        // Staff account
        User::create([
            'user_id' => 'ST_001',
            'name' => 'Nguyễn Văn A',
            'email' => 'staff1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'gender' => 'Nam',
            'phone' => '0901111111',
            'birth_date' => '1995-05-20',
            'address' => 'TP. Hồ Chí Minh, Việt Nam',
        ]);

        User::create([
            'user_id' => 'ST_002',
            'name' => 'Trần Thị B',
            'email' => 'staff2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'gender' => 'Nữ',
            'phone' => '0902222222',
            'birth_date' => '1998-08-30',
            'address' => 'Đà Nẵng, Việt Nam',
        ]);

        // User/Customer accounts
        User::create([
            'user_id' => 'US_001',
            'name' => 'Lê Minh C',
            'email' => 'user1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'gender' => 'Nam',
            'phone' => '0903333333',
            'birth_date' => '1996-03-12',
            'address' => 'Cần Thơ, Việt Nam',
        ]);

        User::create([
            'user_id' => 'US_002',
            'name' => 'Phạm Thị D',
            'email' => 'user2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'gender' => 'Nữ',
            'phone' => '0904444444',
            'birth_date' => '1997-07-22',
            'address' => 'Huế, Việt Nam',
        ]);

        User::create([
            'user_id' => 'US_003',
            'name' => 'Hoàng Văn E',
            'email' => 'user3@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'gender' => 'Nam',
            'phone' => '0905555555',
            'birth_date' => '1999-11-08',
            'address' => 'Hải Phòng, Việt Nam',
        ]);
    }
}
