<?php

namespace Database\Seeders;

use App\Models\Interview;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo interview records cho candidates có status = 'interviewed'
     */
    public function run(): void
    {
        // Interview cho US_003 (status: interviewed)
        Interview::create([
            'user_id' => 'US_003',
            'interviewer_id' => 1,
            'scheduled_at' => '2024-01-20 10:00:00',
            'result' => 'pending',
            'notes' => 'Phỏng vấn thứ nhất - vị trí Nhân Viên Nhân Sự',
        ]);

        // Interview cho US_002 (có thể add nếu cần)
        // Interview::create([
        //     'user_id' => 'US_002',
        //     'scheduled_at' => '2024-01-22 14:00:00',
        //     'result' => 'pending',
        //     'notes' => 'Phỏng vấn Frontend Developer',
        // ]);
    }
}
