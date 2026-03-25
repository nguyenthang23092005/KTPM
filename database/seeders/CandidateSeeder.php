<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo candidates từ user accounts (kế thừa từ users)
     */
    public function run(): void
    {
        // Candidate từ US_002
        Candidate::create([
            'user_id' => 'US_002',
            'job_id' => null,
            'position_applied' => 'Frontend Developer',
            'experience' => '2 năm kinh nghiệm',
            'education' => 'Đại học Công nghệ Thông tin',
            'status' => 'Đã duyệt CV',
            'applied_date' => '2024-01-10',
            'notes' => 'Có portfolio sản phẩm, tốt về giao tiếp',
        ]);

        // Candidate từ US_003
        Candidate::create([
            'user_id' => 'US_003',
            'job_id' => null,
            'position_applied' => 'Nhân Viên Nhân Sự',
            'experience' => '1.5 năm kinh nghiệm',
            'education' => 'Đại học Quốc tế',
            'status' => 'Phỏng vấn',
            'applied_date' => '2024-01-12',
            'notes' => 'Xuất sắc trong giao tiếp, sẵn sàng làm việc',
        ]);

        // Tạo thêm users làm candidates
        $candidateUsers = [
            [
                'user_id' => 'US_004',
                'name' => 'Ngô Tuấn Hùng',
                'email' => 'user4@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'gender' => 'Nam',
                'phone' => '0923456789',
                'birth_date' => '1998-06-15',
                'address' => '654 Trần Hưng Đạo, Quận 1, TPHCM',
            ],
            [
                'user_id' => 'US_005',
                'name' => 'Hoàng Yến Nhi',
                'email' => 'user5@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'gender' => 'Nữ',
                'phone' => '0945678901',
                'birth_date' => '1999-03-20',
                'address' => '321 Nguyễn Trãi, Quận 5, TPHCM',
            ],
        ];

        // Create users and their candidate profiles
        foreach ($candidateUsers as $userData) {
            User::create($userData);

            // Create corresponding candidate profile
            Candidate::create([
                'user_id' => $userData['user_id'],
                'job_id' => null,
                'position_applied' => $userData['user_id'] === 'US_004' ? 'Kế Toán Viên' : 'Chuyên Viên Marketing',
                'experience' => $userData['user_id'] === 'US_004' ? '4 năm kinh nghiệm' : '2.5 năm kinh nghiệm',
                'education' => $userData['user_id'] === 'US_004' ? 'Đại học Kinh Tế TPHCM' : 'Đại học Kinh Tế Tài Chính',
                'status' => 'Đang chờ',
                'applied_date' => now()->subDays(rand(1, 10)),
                'notes' => $userData['user_id'] === 'US_004' ? 'Kinh nghiệm dày dặn, tốt' : 'Kinh nghiệm Facebook/Google Ads, sáng tạo',
            ]);
        }
    }
}

