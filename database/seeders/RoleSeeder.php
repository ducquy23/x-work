<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'nhan-vien' => 'Nhân viên',
            'truong-phong' => 'Trưởng phòng',
            'giam-doc-da' => 'Giám đốc DA',
            'ban-dieu-hanh' => 'Ban điều hành',
            'chu-tich' => 'Chủ tịch',
        ];

        foreach ($roles as $name => $displayName) {
            Role::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }
    }
}
