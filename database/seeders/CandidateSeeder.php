<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\JobPosting;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $jobIdsByTitle = JobPosting::query()->pluck('job_id', 'title');

        $candidates = [
            [
                'user_id' => 'US_001',
                'job_title' => 'Frontend Developer React',
                'status' => 'Đã duyệt CV',
                'experience' => '2 năm kinh nghiệm',
                'education' => 'Đại học Công nghệ TPHCM',
                'notes' => 'Có portfolio github, tốt giao tiếp',
            ],
            [
                'user_id' => 'US_002',
                'job_title' => 'Frontend Developer React',
                'status' => 'Phỏng vấn',
                'experience' => '2.5 năm kinh nghiệm',
                'education' => 'Đại học FPT',
                'notes' => 'Good ui/ux design sense, tốt',
            ],
            [
                'user_id' => 'US_003',
                'job_title' => 'Frontend Developer React',
                'status' => 'Đã nhận việc',
                'experience' => '1.5 năm kinh nghiệm',
                'education' => 'Cao đẳng HNIT',
                'notes' => 'Reaction nhanh, learning ability tốt',
            ],
            [
                'user_id' => 'US_004',
                'job_title' => 'Frontend Developer React',
                'status' => 'Từ chối',
                'experience' => '1 năm kinh nghiệm',
                'education' => 'Tự học online',
                'notes' => 'Có các dự án nhỏ, tính chuyên cần',
            ],
            [
                'user_id' => 'US_006',
                'job_title' => 'Lập Trình Viên PHP/Laravel',
                'status' => 'Đang chờ',
                'experience' => '3 năm kinh nghiệm',
                'education' => 'Đại học Bách Khoa Hà Nội',
                'notes' => 'Kinh nghiệm Enterprise, thái độ tốt',
            ],
            [
                'user_id' => 'US_007',
                'job_title' => 'Lập Trình Viên PHP/Laravel',
                'status' => 'Phỏng vấn',
                'experience' => '2.5 năm kinh nghiệm',
                'education' => 'Đại học Kinh tế Quốc dân',
                'notes' => 'Thành thạo clean code, testing',
            ],
            [
                'user_id' => 'US_008',
                'job_title' => 'Full Stack Developer',
                'status' => 'Đã duyệt CV',
                'experience' => '4 năm kinh nghiệm',
                'education' => 'Đại học Sài Gòn',
                'notes' => 'Kinh nghiệm startup, growth mindset',
            ],
            [
                'user_id' => 'US_009',
                'job_title' => 'Nhân Viên Nhân Sự',
                'status' => 'Đang chờ',
                'experience' => '1 năm kinh nghiệm',
                'education' => 'Đại học Hà Nội',
                'notes' => 'Communication skills tốt, cẩn thận',
            ],
            [
                'user_id' => 'US_010',
                'job_title' => 'Recruitment Officer',
                'status' => 'Đã duyệt CV',
                'experience' => '2 năm kinh nghiệm',
                'education' => 'Đại học Ngoại thương',
                'notes' => 'Có network tốt, target driven',
            ],
            [
                'user_id' => 'US_011',
                'job_title' => 'Kế Toán Viên',
                'status' => 'Phỏng vấn',
                'experience' => '3 năm kinh nghiệm',
                'education' => 'Đại học Kinh Tế TPHCM',
                'notes' => 'Thành thạo phần mềm kế toán, cẩn thận',
            ],
            [
                'user_id' => 'US_012',
                'job_title' => 'Trưởng Phòng Kế Toán',
                'status' => 'Đang chờ',
                'experience' => '6 năm kinh nghiệm',
                'education' => 'Đại học Kinh Tế Quốc dân',
                'notes' => 'Quản lý nhiều phòng ban, kỹ năng cao',
            ],
            [
                'user_id' => 'US_013',
                'job_title' => 'Chuyên Viên Digital Marketing',
                'status' => 'Đã duyệt CV',
                'experience' => '2 năm kinh nghiệm',
                'education' => 'Đại học Thương mại',
                'notes' => 'Sáng tạo campaigns, data-driven',
            ],
            [
                'user_id' => 'US_014',
                'job_title' => 'Content Creator',
                'status' => 'Phỏng vấn',
                'experience' => '1.5 năm kinh nghiệm',
                'education' => 'Đại học Văn hóa Thông tin',
                'notes' => 'Creative writing, graphics design',
            ],
            [
                'user_id' => 'US_015',
                'job_title' => 'Chuyên Viên Digital Marketing',
                'status' => 'Từ chối',
                'experience' => '1 năm kinh nghiệm',
                'education' => 'Tự học online',
                'notes' => 'Tìm hiểu nhiều nhưng kiến thức sâu còn hạn chế',
            ],
        ];

        foreach ($candidates as $candidate) {
            Candidate::updateOrCreate(
                ['user_id' => $candidate['user_id']],
                [
                    'job_id' => $jobIdsByTitle[$candidate['job_title']] ?? null,
                    'position_applied' => $candidate['job_title'],
                    'status' => $candidate['status'],
                    'experience' => $candidate['experience'],
                    'education' => $candidate['education'],
                    'notes' => $candidate['notes'],
                    'applied_date' => now()->subDays(rand(1, 15))->toDateString(),
                ]
            );
        }
    }
}