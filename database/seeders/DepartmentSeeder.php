<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Phòng Kỹ thuật',
                'description' => 'Phòng ban chịu trách nhiệm về phát triển và bảo trì hệ thống kỹ thuật',
            ],
            [
                'name' => 'Phòng Kinh doanh',
                'description' => 'Phòng ban chịu trách nhiệm về hoạt động kinh doanh và phát triển thị trường',
            ],
            [
                'name' => 'Phòng Nhân sự',
                'description' => 'Phòng ban quản lý nhân sự và tuyển dụng',
            ],
            [
                'name' => 'Phòng Tài chính',
                'description' => 'Phòng ban quản lý tài chính và kế toán',
            ],
            [
                'name' => 'Phòng Marketing',
                'description' => 'Phòng ban chịu trách nhiệm về marketing và truyền thông',
            ],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}

