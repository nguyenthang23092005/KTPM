<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo employee từ user accounts (kế thừa từ users)
     */
    public function run(): void
    {
        // Quản lý phòng Nhân sự (ST_001)
        Employee::create([
            'user_id' => 'ST_001',
            'position' => 'Trưởng phòng Nhân sự',
            'department_id' => 'DEPT001',
            'identity_card' => '012345678901',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'Hà Nội',
            'current_address' => '123 Đường Láng, Đống Đa, Hà Nội',
            'start_date' => '2015-02-01',
            'status' => 'Đang làm',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'avatar_path' => 'employees/ST_001/avatar.jpg',
            'cv_path' => 'employees/ST_001/cv.pdf',
            'contract_path' => 'employees/ST_001/contract.pdf',
            'notes' => 'Trưởng phòng Nhân sự',
        ]);

        // Quản lý phòng Kế toán (ST_002)
        Employee::create([
            'user_id' => 'ST_002',
            'position' => 'Trưởng phòng Kế toán',
            'department_id' => 'DEPT002',
            'identity_card' => '123456789012',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'TP Hồ Chí Minh',
            'current_address' => '456 Nguyễn Hữu Cảnh, Bình Thạnh, TPHCM',
            'start_date' => '2016-03-15',
            'status' => 'Đang làm',
            'ethnicity' => 'Kinh',
            'religion' => 'Catholich',
            'nationality' => 'Việt Nam',
            'avatar_path' => 'employees/ST_002/avatar.jpg',
            'cv_path' => 'employees/ST_002/cv.pdf',
            'contract_path' => 'employees/ST_002/contract.pdf',
            'notes' => 'Trưởng phòng Kế toán',
        ]);

        // Admin user có thể có profile nhân viên
        Employee::create([
            'user_id' => 'AD_001',
            'position' => 'Giám đốc điều hành',
            'department_id' => 'DEPT001',
            'identity_card' => '234567890123',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'Hà Nội',
            'current_address' => '789 Cách Mạng Tháng 8, Hai Bà Trưng, Hà Nội',
            'start_date' => '2010-01-01',
            'status' => 'Đang làm',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'avatar_path' => 'employees/AD_001/avatar.jpg',
            'cv_path' => 'employees/AD_001/cv.pdf',
            'contract_path' => 'employees/AD_001/contract.pdf',
            'notes' => 'Admin - Giám đốc hệ thống',
        ]);

        // Regular users có thể có profile nhân viên (applicants hoặc candidates)
        Employee::create([
            'user_id' => 'US_001',
            'position' => 'Ứng viên',
            'department_id' => 'DEPT003',
            'identity_card' => '345678901234',
            'marital_status' => 'Độc thân',
            'hometown' => 'Đà Nẵng',
            'current_address' => '321 Lạc Long Quân, Tân Bình, TPHCM',
            'start_date' => '2024-01-15',
            'status' => 'Đang làm',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'avatar_path' => 'employees/US_001/avatar.jpg',
            'cv_path' => 'employees/US_001/cv.pdf',
            'contract_path' => 'employees/US_001/contract.pdf',
            'notes' => 'Ứng viên mới',
        ]);
    }
}

