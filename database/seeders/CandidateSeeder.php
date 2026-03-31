<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\JobPosting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo candidates profile cho 15 user accounts
     */
    public function run(): void
    {
        $jobIdsByTitle = JobPosting::query()->pluck('job_id', 'title');

        $candidates = [
            // Frontend candidates
            ['US_001', 'Frontend Developer React', 'Đã duyệt CV', '2 năm kinh nghiệm', 'Đại học Công nghệ TPHCM', 'Có portfolio github, tốt giao tiếp'],
            ['US_002', 'Frontend Developer React', 'Phỏng vấn', '2.5 năm kinh nghiệm', 'Đại học FPT', 'Good ui/ux design sense, tốt'],
            ['US_003', 'Frontend Developer React', 'Đã nhận việc', '1.5 năm kinh nghiệm', 'Cao đẳng HNIT', 'Reaction nhanh, learning ability tốt'],
            ['US_004', 'Frontend Developer React', 'Từ chối', '1 năm kinh nghiệm', 'Tự học online', 'Có các dự án nhỏ, tính chuyên cần'],
            
            // Backend candidates
            ['US_006', 'Lập Trình Viên PHP/Laravel', 'Đang chờ', '3 năm kinh nghiệm', 'Đại học Bách Khoa Hà Nội', 'Kinh nghiệm Enterprise, thái độ tốt'],
            ['US_007', 'Lập Trình Viên PHP/Laravel', 'Phỏng vấn', '2.5 năm kinh nghiệm', 'Đại học Kinh tế Quốc dân', 'Thành thạo clean code, testing'],
            ['US_008', 'Full Stack Developer', 'Đã duyệt CV', '4 năm kinh nghiệm', 'Đại học Sài Gòn', 'Kinh nghiệm startup, growth mindset'],
            
            // HR candidates
            ['US_009', 'Nhân Viên Nhân Sự', 'Đang chờ', '1 năm kinh nghiệm', 'Đại học Hà Nội', 'Communication skills tốt, cẩn thận'],
            ['US_010', 'Recruitment Officer', 'Đã duyệt CV', '2 năm kinh nghiệm', 'Đại học Ngoại thương', 'Có network tốt, target driven'],
            
            // Accounting candidates
            ['US_011', 'Kế Toán Viên', 'Phỏng vấn', '3 năm kinh nghiệm', 'Đại học Kinh Tế TPHCM', 'Thành thạo phần mềm kế toán, cẩn thận'],
            ['US_012', 'Trưởng Phòng Kế Toán', 'Đang chờ', '6 năm kinh nghiệm', 'Đại học Kinh Tế Quốc dân', 'Quản lý nhiều phòng ban, kỹ năng cao'],
            
            // Marketing candidates
            ['US_013', 'Chuyên Viên Digital Marketing', 'Đã duyệt CV', '2 năm kinh nghiệm', 'Đại học Thương mại', 'Sáng tạo campaigns, data-driven'],
            ['US_014', 'Content Creator', 'Phỏng vấn', '1.5 năm kinh nghiệm', 'Đại học Văn hóa Thông tin', 'Creative writing, graphics design'],
            ['US_015', 'Chuyên Viên Digital Marketing', 'Từ chối', '1 năm kinh nghiệm', 'Tự học online', 'Tìm hiểu nhiều nhưng kiến thức sâu còn hạn chế'],
        ];

        foreach ($candidates as $candidate) {
            Candidate::updateOrCreate([
                'user_id' => $candidate[0],
            ], [
                'job_id' => $jobIdsByTitle[$candidate[1]] ?? null,
                'position_applied' => $candidate[1],
                'status' => $candidate[2],
                'experience' => $candidate[3],
                'education' => $candidate[4],
                'notes' => $candidate[5],
                'applied_date' => now()->subDays(rand(1, 15)),
            ]);
        }
    }
}

