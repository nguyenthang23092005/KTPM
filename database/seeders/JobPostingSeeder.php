<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Lập Trình Viên PHP',
                'description' => 'Tuyển dụng lập trình viên PHP với kinh nghiệm 2+ năm. Yêu cầu: Thành thạo PHP, Laravel.',
                'requirements' => 'PHP, Laravel, MySQL, Git',
                'salary_min' => 15000000,
                'salary_max' => 25000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng IT',
                'status' => 'active',
                'deadline' => now()->addDays(30),
            ],
            [
                'title' => 'Frontend Developer',
                'description' => 'Phát triển giao diện web với React/Vue. Kinh nghiệm 1.5+ năm.',
                'requirements' => 'React, Vue.js, HTML/CSS, JavaScript',
                'salary_min' => 12000000,
                'salary_max' => 20000000,
                'location' => 'TP. Hồ Chí Minh',
                'department' => 'Phòng IT',
                'status' => 'active',
                'deadline' => now()->addDays(25),
            ],
            [
                'title' => 'Nhân Viên Nhân Sự',
                'description' => 'Tuyển dụng nhân viên nhân sự hỗ trợ. Tính chuyên nghiệp, cẩn thận.',
                'requirements' => 'Tốt nghiệp cao đẳng/đại học, khéo léo giao tiếp',
                'salary_min' => 8000000,
                'salary_max' => 12000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng Nhân Sự',
                'status' => 'active',
                'deadline' => now()->addDays(20),
            ],
            [
                'title' => 'Kế Toán Viên',
                'description' => 'Hỗ trợ kế toán công ti. Chasty tính toán, tổng hợp báo cáo tài chính.',
                'requirements' => 'Cấp chứng chỉ kế toán, Excel, phần mềm kế toán',
                'salary_min' => 10000000,
                'salary_max' => 16000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng Kế Toán',
                'status' => 'active',
                'deadline' => now()->addDays(28),
            ],
            [
                'title' => 'Chuyên Viên Marketing',
                'description' => 'Quản lý các chiến dịch quảng cáo trên mạng xã hội và email.',
                'requirements' => 'Marketing, Social Media, Google Ads, Analytics',
                'salary_min' => 11000000,
                'salary_max' => 18000000,
                'location' => 'TP. Hồ Chí Minh',
                'department' => 'Phòng Marketing',
                'status' => 'active',
                'deadline' => now()->addDays(35),
            ],
        ];

        foreach ($jobs as $job) {
            \App\Models\JobPosting::create($job);
        }
    }
}
