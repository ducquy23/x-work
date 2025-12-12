<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();

        $projects = [
            [
                'name' => 'Dự án Phát triển Website',
                'description' => 'Xây dựng website mới cho công ty với các tính năng hiện đại',
                'department_id' => $departments->where('name', 'Phòng Kỹ thuật')->first()?->id,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(4),
                'status' => 'in_progress',
            ],
            [
                'name' => 'Dự án Nâng cấp Hệ thống',
                'description' => 'Nâng cấp hệ thống quản lý nội bộ lên phiên bản mới',
                'department_id' => $departments->where('name', 'Phòng Kỹ thuật')->first()?->id,
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(3),
                'status' => 'in_progress',
            ],
            [
                'name' => 'Chiến dịch Marketing Q1',
                'description' => 'Chiến dịch marketing cho quý 1 năm 2025',
                'department_id' => $departments->where('name', 'Phòng Marketing')->first()?->id,
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonth(),
                'status' => 'in_progress',
            ],
            [
                'name' => 'Dự án Mở rộng Thị trường',
                'description' => 'Mở rộng thị trường ra các tỉnh thành mới',
                'department_id' => $departments->where('name', 'Phòng Kinh doanh')->first()?->id,
                'start_date' => now()->subMonths(4),
                'end_date' => now()->addMonths(2),
                'status' => 'in_progress',
            ],
            [
                'name' => 'Dự án Tuyển dụng Nhân sự',
                'description' => 'Tuyển dụng nhân sự cho các phòng ban',
                'department_id' => $departments->where('name', 'Phòng Nhân sự')->first()?->id,
                'start_date' => now()->subMonths(1),
                'end_date' => now()->addMonths(2),
                'status' => 'planning',
            ],
            [
                'name' => 'Dự án Tối ưu Tài chính',
                'description' => 'Tối ưu hóa quy trình tài chính và kế toán',
                'department_id' => $departments->where('name', 'Phòng Tài chính')->first()?->id,
                'start_date' => now()->subWeeks(2),
                'end_date' => now()->addMonths(3),
                'status' => 'planning',
            ],
            [
                'name' => 'Dự án Mobile App',
                'description' => 'Phát triển ứng dụng di động cho khách hàng',
                'department_id' => $departments->where('name', 'Phòng Kỹ thuật')->first()?->id,
                'start_date' => now()->addWeek(),
                'end_date' => now()->addMonths(6),
                'status' => 'planning',
            ],
            [
                'name' => 'Dự án Đào tạo Nội bộ',
                'description' => 'Tổ chức các khóa đào tạo nội bộ cho nhân viên',
                'department_id' => $departments->where('name', 'Phòng Nhân sự')->first()?->id,
                'start_date' => now()->subWeeks(3),
                'end_date' => now()->addMonths(1),
                'status' => 'in_progress',
            ],
        ];

        foreach ($projects as $project) {
            Project::firstOrCreate(
                ['name' => $project['name']],
                $project
            );
        }
    }
}

