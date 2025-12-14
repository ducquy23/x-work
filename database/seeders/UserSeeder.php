<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();

        // Tạo admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Quản trị viên',
                'password' => Hash::make('password'),
                'department_id' => $departments->first()?->id,
                'position' => 'Quản trị viên hệ thống',
                'status' => 'active',
            ]
        );

        // Gán role cho admin (nếu có role super_admin hoặc admin)
        $adminRole = Role::where('name', 'chu-tich')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // Tạo users cho từng phòng ban
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nva@example.com',
                'position' => 'Lập trình viên',
                'role' => 'nhan-vien',
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'ttb@example.com',
                'position' => 'Lập trình viên Senior',
                'role' => 'nhan-vien',
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'lvc@example.com',
                'position' => 'Trưởng phòng Kỹ thuật',
                'role' => 'truong-phong',
            ],
            [
                'name' => 'Phạm Thị D',
                'email' => 'ptd@example.com',
                'position' => 'Nhân viên Kinh doanh',
                'role' => 'nhan-vien',
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'hve@example.com',
                'position' => 'Trưởng phòng Kinh doanh',
                'role' => 'truong-phong',
            ],
            [
                'name' => 'Vũ Thị F',
                'email' => 'vtf@example.com',
                'position' => 'Chuyên viên Nhân sự',
                'role' => 'nhan-vien',
            ],
            [
                'name' => 'Đặng Văn G',
                'email' => 'dvg@example.com',
                'position' => 'Trưởng phòng Nhân sự',
                'role' => 'truong-phong',
            ],
            [
                'name' => 'Bùi Thị H',
                'email' => 'bth@example.com',
                'position' => 'Kế toán viên',
                'role' => 'nhan-vien',
            ],
            [
                'name' => 'Ngô Văn I',
                'email' => 'nvi@example.com',
                'position' => 'Trưởng phòng Tài chính',
                'role' => 'truong-phong',
            ],
            [
                'name' => 'Đỗ Thị K',
                'email' => 'dtk@example.com',
                'position' => 'Chuyên viên Marketing',
                'role' => 'nhan-vien',
            ],
        ];

        $departmentIndex = 0;
        foreach ($users as $userData) {
            $department = $departments->get($departmentIndex % $departments->count());
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'department_id' => $department->id,
                    'position' => $userData['position'],
                    'status' => 'active',
                ]
            );

            // Gán role
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
            }

            $departmentIndex++;
        }
    }
}


