<?php

namespace Database\Seeders;

use App\Models\Interview;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo interview records cho candidates
     */
    public function run(): void
    {
        $interviews = [
            // Frontend candidates interviews
            ['US_002', '2024-01-20 10:00:00', 'pass', 'Frontend Developer - Phỏng vấn kỹ thuật pass'],
            ['US_003', '2024-01-22 14:00:00', 'pass', 'Frontend Developer - Vòng phỏng vấn cuối pass'],
            ['US_004', '2024-01-18 09:00:00', 'fail', 'Frontend Developer - Kiến thức cơ bản còn yếu'],
            
            // Backend candidates interviews
            ['US_007', '2024-01-25 11:00:00', 'pending', 'Lập Trình Viên PHP/Laravel - Đang chờ kết quả'],
            ['US_008', '2024-01-28 15:00:00', 'pass', 'Full Stack Developer - Kinh nghiệm tốt, tư duy tốt'],
            
            // HR candidates interviews
            ['US_010', '2024-01-16 10:30:00', 'pass', 'Recruitment Officer - Giao tiếp tốt, hiểu rõ quy trình HR'],
            
            // Accounting candidates interviews
            ['US_011', '2024-01-26 13:00:00', 'pending', 'Kế Toán Viên - Chờ kết quả vòng 2'],
            
            // Marketing candidates interviews
            ['US_014', '2024-01-27 09:30:00', 'pending', 'Content Creator - Đang trong quá trình thử việc'],
            ['US_015', '2024-01-19 14:00:00', 'fail', 'Chuyên Viên Digital Marketing - Kiến thức Marketing sâu còn hạn chế'],
            
            // Additional interviews
            ['US_006', '2024-02-01 10:00:00', 'pending', 'Lập Trình Viên PHP/Laravel - Sắp sở hữu vòng phỏng vấn'],
            ['US_009', '2024-02-02 11:00:00', 'pending', 'Nhân Viên Nhân Sự - Sắp xếp lịch test năng lực'],
            ['US_013', '2024-02-03 15:00:00', 'pending', 'Chuyên Viên Digital Marketing - Phỏng vấn vòng 2'],
        ];

        foreach ($interviews as $interview) {
            Interview::updateOrCreate([
                'user_id' => $interview[0],
                'scheduled_at' => $interview[1],
            ], [
                'result' => $interview[2],
                'notes' => $interview[3],
            ]);
        }
    }
}
