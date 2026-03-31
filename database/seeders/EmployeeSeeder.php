<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo employee profile cho 2 admin + 8 staff accounts
     */
    public function run(): void
    {
        // Admin accounts
        Employee::updateOrCreate([
            'user_id' => 'AD_001',
        ], [
            'position' => 'Giám đốc điều hành',
            'department_id' => 'DEPT001',
            'identity_card' => '012345678901',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'Hà Nội',
            'current_address' => '123 Tòa FLC, Đống Đa, Hà Nội',
            'start_date' => '2010-01-01',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Quản trị kinh doanh',
            'school_name' => 'Đại học Kinh tế Quốc dân',
            'certificates' => 'Chứng chỉ Quản trị doanh nghiệp, Chứng chỉ lãnh đạo cấp cao',
            'language_certificates' => 'IELTS 7.5',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Admin - Giám đốc hệ thống',
        ]);

        Employee::updateOrCreate([
            'user_id' => 'AD_002',
        ], [
            'position' => 'Phó giám đốc',
            'department_id' => 'DEPT003',
            'identity_card' => '111111111111',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'Hà Nội',
            'current_address' => '456 Phố Huế, Hoàn Kiếm, Hà Nội',
            'start_date' => '2012-03-15',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Công nghệ thông tin',
            'school_name' => 'Đại học Bách Khoa Hà Nội',
            'certificates' => 'CCNA, AWS Cloud Practitioner',
            'language_certificates' => 'TOEIC 850',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Phó giám đốc IT',
        ]);

        // HR Department (DEPT001)
        Employee::updateOrCreate([
            'user_id' => 'ST_001',
        ], [
            'position' => 'Trưởng phòng Nhân sự',
            'department_id' => 'DEPT001',
            'identity_card' => '012345678902',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'Hà Nội',
            'current_address' => '123 Đường Láng, Đống Đa, Hà Nội',
            'start_date' => '2015-02-01',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Quản trị nhân lực',
            'school_name' => 'Đại học Lao động - Xã hội',
            'certificates' => 'Chứng chỉ C&B, Chứng chỉ Tuyển dụng chuyên sâu',
            'language_certificates' => 'TOEIC 700',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Quản lý tuyển dụng và quản lý nhân sự',
        ]);

        Employee::updateOrCreate([
            'user_id' => 'ST_004',
        ], [
            'position' => 'Chuyên viên Nhân sự',
            'department_id' => 'DEPT001',
            'identity_card' => '012345678904',
            'marital_status' => 'Độc thân',
            'hometown' => 'Hà Nội',
            'current_address' => '789 Nguyễn Chí Thanh, Thanh Xuân, Hà Nội',
            'start_date' => '2018-06-15',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Quản trị kinh doanh',
            'school_name' => 'Đại học Hà Nội',
            'certificates' => 'Chứng chỉ Tin học văn phòng MOS',
            'language_certificates' => 'TOEIC 650',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Hỗ trợ tuyển dụng, đào tạo nhân viên',
        ]);

        // Accounting Department (DEPT002)
        Employee::updateOrCreate([
            'user_id' => 'ST_002',
        ], [
            'position' => 'Trưởng phòng Kế toán',
            'department_id' => 'DEPT002',
            'identity_card' => '123456789012',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'TP Hồ Chí Minh',
            'current_address' => '456 Nguyễn Hữu Cảnh, Bình Thạnh, TPHCM',
            'start_date' => '2016-03-15',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Kế toán',
            'school_name' => 'Đại học Kinh tế TP.HCM',
            'certificates' => 'Chứng chỉ Kế toán trưởng, Chứng chỉ Excel nâng cao',
            'language_certificates' => 'TOEIC 620',
            'ethnicity' => 'Kinh',
            'religion' => 'Công giáo',
            'nationality' => 'Việt Nam',
            'notes' => 'Quản lý kế toán tài chính',
        ]);

        Employee::updateOrCreate([
            'user_id' => 'ST_006',
        ], [
            'position' => 'Chuyên viên Kế toán',
            'department_id' => 'DEPT002',
            'identity_card' => '123456789906',
            'marital_status' => 'Độc thân',
            'hometown' => 'Tây Hồ',
            'current_address' => '321 Cộng Hòa, Tây Hồ, Hà Nội',
            'start_date' => '2019-01-20',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Tài chính - Kế toán',
            'school_name' => 'Học viện Tài chính',
            'certificates' => 'MOS Excel, Chứng chỉ nghiệp vụ kế toán tổng hợp',
            'language_certificates' => 'TOEIC 580',
            'ethnicity' => 'Kinh',
            'religion' => 'Phật giáo',
            'nationality' => 'Việt Nam',
            'notes' => 'Hỗ trợ lập báo cáo tài chính',
        ]);

        // IT Department (DEPT003)
        Employee::updateOrCreate([
            'user_id' => 'ST_003',
        ], [
            'position' => 'Trưởng phòng IT',
            'department_id' => 'DEPT003',
            'identity_card' => '234567890123',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'TP Hồ Chí Minh',
            'current_address' => '789 Tôn Đức Thắng, Quận 1, TPHCM',
            'start_date' => '2014-05-10',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Kỹ sư Công nghệ thông tin',
            'school_name' => 'Đại học Công nghệ Thông tin - ĐHQG TP.HCM',
            'certificates' => 'AWS Solutions Architect, Scrum Master',
            'language_certificates' => 'IELTS 6.5',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Quản lý phát triển hệ thống IT',
        ]);

        Employee::updateOrCreate([
            'user_id' => 'ST_005',
        ], [
            'position' => 'Lập trình viên PHP',
            'department_id' => 'DEPT003',
            'identity_card' => '234567890125',
            'marital_status' => 'Độc thân',
            'hometown' => 'Đà Nẵng',
            'current_address' => '654 Trần Phú, Hải Châu, Đà Nẵng',
            'start_date' => '2017-08-01',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Khoa học máy tính',
            'school_name' => 'Đại học Bách Khoa Đà Nẵng',
            'certificates' => 'Laravel Certification, Chứng chỉ MySQL cơ bản',
            'language_certificates' => 'TOEIC 720',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Phát triển backend Laravel',
        ]);

        Employee::updateOrCreate([
            'user_id' => 'ST_007',
        ], [
            'position' => 'Lập trình viên Frontend',
            'department_id' => 'DEPT003',
            'identity_card' => '234567890127',
            'marital_status' => 'Đã kết hôn',
            'hometown' => 'Long Biên',
            'current_address' => '111 Phạm Văn Bạch, Long Biên, Hà Nội',
            'start_date' => '2018-04-15',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Hệ thống thông tin',
            'school_name' => 'Đại học FPT',
            'certificates' => 'Chứng chỉ UI/UX Foundation, MOS PowerPoint',
            'language_certificates' => 'IELTS 6.0',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Phát triển frontend React',
        ]);

        // Marketing Department (DEPT004)
        Employee::updateOrCreate([
            'user_id' => 'ST_008',
        ], [
            'position' => 'Chuyên viên Marketing',
            'department_id' => 'DEPT004',
            'identity_card' => '345678901028',
            'marital_status' => 'Độc thân',
            'hometown' => 'Quận 3',
            'current_address' => '222 Trần Xuân Soạn, Quận 3, TPHCM',
            'start_date' => '2019-09-01',
            'status' => 'Đang làm',
            'education_level' => 'Đại học',
            'degree' => 'Cử nhân Marketing',
            'school_name' => 'Đại học Thương mại',
            'certificates' => 'Google Ads Search Certification, Facebook Blueprint',
            'language_certificates' => 'TOEIC 680',
            'ethnicity' => 'Kinh',
            'religion' => 'Không',
            'nationality' => 'Việt Nam',
            'notes' => 'Quản lý chiến dịch Digital Marketing',
        ]);
    }
}

