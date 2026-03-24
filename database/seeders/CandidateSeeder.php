<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('candidates')->insertOrIgnore([
            [
                'name' => 'Võ Tuấn Anh',
                'email' => 'votuananh@email.com',
                'phone' => '0912345678',
                'position_applied' => 'Lập Trình Viên PHP',
                'experience' => '3 năm',
                'education' => 'Đại học Công Nghệ Thông Tin',
                'status' => 'new',
                'notes' => 'GPA 3.5/4.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Đinh Minh Quân',
                'email' => 'dinhminquan@email.com',
                'phone' => '0987654321',
                'position_applied' => 'Frontend Developer',
                'experience' => '2 năm',
                'education' => 'Đại học Khoa Học Tự Nhiên',
                'status' => 'reviewed',
                'notes' => 'Tốt, có portfolio sản phẩm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trịnh Thu Hương',
                'email' => 'trinhthuhung@email.com',
                'phone' => '0934567890',
                'position_applied' => 'Nhân Viên Nhân Sự',
                'experience' => '1.5 năm',
                'education' => 'Đại học Quốc Tế',
                'status' => 'interviewed',
                'notes' => 'Xuất sắc trong giao tiếp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ngô Tuấn Hùng',
                'email' => 'ngothuanhung@email.com',
                'phone' => '0923456789',
                'position_applied' => 'Kế Toán Viên',
                'experience' => '4 năm',
                'education' => 'Đại học Kinh Tế Tp.HCM',
                'status' => 'shortlisted',
                'notes' => 'Kinh nghiệm dày dặn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hoàng Yến Nhi',
                'email' => 'hoangyen@email.com',
                'phone' => '0945678901',
                'position_applied' => 'Chuyên Viên Marketing',
                'experience' => '2.5 năm',
                'education' => 'Đại học Kinh Tế Tài Chính',
                'status' => 'new',
                'notes' => 'Có kinh nghiệm làm Facebook/Google Ads',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
