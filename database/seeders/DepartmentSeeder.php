<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insertOrIgnore([
            [
                'name' => 'Phòng IT',
                'description' => 'Bộ phòng công nghệ thông tin',
                'manager' => 'Nguyễn Văn D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phòng Nhân Sự',
                'description' => 'Bộ phòng quản lý nhân sự',
                'manager' => 'Trần Thị E',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phòng Kế Toán',
                'description' => 'Bộ phòng tài chính kế toán',
                'manager' => 'Lê Minh F',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phòng Marketing',
                'description' => 'Bộ phòng tiếp thị',
                'manager' => 'Phạm Thị G',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
