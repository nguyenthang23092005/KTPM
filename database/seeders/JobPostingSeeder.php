<?php

namespace Database\Seeders;

use App\Models\JobPosting;
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
            // IT/Tech Jobs
            [
                'title' => 'Lập Trình Viên PHP/Laravel',
                'description' => 'Tuyển dụng lập trình viên PHP với kinh nghiệm 2+ năm. Yêu cầu: Thành thạo PHP, Laravel, MySQL.',
                'requirements' => 'PHP 8+, Laravel, MySQL, Git, Docker',
                'salary_min' => 15000000,
                'salary_max' => 25000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng IT',
                'status' => 'active',
                'deadline' => now()->addDays(30),
            ],
            [
                'title' => 'Frontend Developer React',
                'description' => 'Phát triển giao diện web với React. Kinh nghiệm 1.5+ năm trong phát triển frontend.',
                'requirements' => 'React, TypeScript, Tailwind CSS, Redux, Git',
                'salary_min' => 12000000,
                'salary_max' => 20000000,
                'location' => 'TP. Hồ Chí Minh',
                'department' => 'Phòng IT',
                'status' => 'active',
                'deadline' => now()->subDays(5), // Hết hạn 5 ngày trước
            ],
            [
                'title' => 'Full Stack Developer',
                'description' => 'Cần full stack developer có khả năng xử lý cả backend và frontend. Kinh nghiệm 3+ năm.',
                'requirements' => 'Laravel, React, MySQL, Redis, AWS',
                'salary_min' => 20000000,
                'salary_max' => 32000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng IT',
                'status' => 'active',
                'deadline' => now()->subDays(1), // Hết hạn 1 ngày trước
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Quản lý infrastructure, deployment và monitoring. Kinh nghiệm 2+ năm với Docker, Kubernetes.',
                'requirements' => 'Docker, Kubernetes, Linux, CI/CD, AWS/Azure',
                'salary_min' => 18000000,
                'salary_max' => 28000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng IT',
                'status' => 'active',
                'deadline' => now()->addDays(35),
            ],
            
            // HR/Admin Jobs
            [
                'title' => 'Nhân Viên Nhân Sự',
                'description' => 'Tuyển dụng nhân viên nhân sự hỗ trợ quá trình tuyển dụng, đào tạo. Tính chuyên nghiệp và cẩn thận.',
                'requirements' => 'Tốt nghiệp cao đẳng/đại học, giao tiếp tốt, tiếng Anh cơ bản',
                'salary_min' => 8000000,
                'salary_max' => 12000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng Nhân Sự',
                'status' => 'active',
                'deadline' => now()->addDays(20),
            ],
            [
                'title' => 'Recruitment Officer',
                'description' => 'Tuyển dụng cho các vị trí khác nhau. Cần kỹ năng tương tác tốt với ứng viên.',
                'requirements' => 'Kinh nghiệm tuyển dụng 1+ năm, tiếng Anh, Microsoft Office thành thạo',
                'salary_min' => 11000000,
                'salary_max' => 16000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng Nhân Sự',
                'status' => 'active',
                'deadline' => now()->subDays(10), // Hết hạn 10 ngày trước
            ],
            
            // Finance Jobs
            [
                'title' => 'Kế Toán Viên',
                'description' => 'Hỗ trợ kế toán công ty. Cẩn thận tính toán, tổng hợp báo cáo tài chính hàng tháng.',
                'requirements' => 'Cấp chứng chỉ kế toán, Excel thành thạo, phần mềm kế toán',
                'salary_min' => 10000000,
                'salary_max' => 16000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng Kế Toán',
                'status' => 'active',
                'deadline' => now()->addDays(28),
            ],
            [
                'title' => 'Trưởng Phòng Kế Toán',
                'description' => 'Quản lý phòng kế toán, báo cáo tài chính, kiểm soát chi phí. Kinh nghiệm 5+ năm.',
                'requirements' => 'Bằng cấp kế toán cao, kinh nghiệm quản lý phòng ban, tiếng Anh',
                'salary_min' => 25000000,
                'salary_max' => 40000000,
                'location' => 'Hà Nội',
                'department' => 'Phòng Kế Toán',
                'status' => 'active',
                'deadline' => now()->addDays(30),
            ],
            
            // Marketing Jobs
            [
                'title' => 'Chuyên Viên Digital Marketing',
                'description' => 'Quản lý các chiến dịch quảng cáo trên Facebook, Google Ads. Kinh nghiệm 2+ năm.',
                'requirements' => 'Facebook Ads, Google Ads, Analytics, Content Marketing',
                'salary_min' => 11000000,
                'salary_max' => 18000000,
                'location' => 'TP. Hồ Chí Minh',
                'department' => 'Phòng Marketing',
                'status' => 'active',
                'deadline' => now()->addDays(35),
            ],
            [
                'title' => 'Content Creator',
                'description' => 'Tạo nội dung cho website, social media, quảng cáo. Yêu cầu sáng tạo, keenhab marketing.',
                'requirements' => 'Năng lực viết lách, Adobe Creative Suite, SEO cơ bản, tiếng Anh',
                'salary_min' => 9000000,
                'salary_max' => 14000000,
                'location' => 'TP. Hồ Chí Minh',
                'department' => 'Phòng Marketing',
                'status' => 'active',
                'deadline' => now()->addDays(32),
            ],
        ];

        foreach ($jobs as $job) {
            JobPosting::updateOrCreate(
                ['title' => $job['title']],
                $job
            );
        }
    }
}
