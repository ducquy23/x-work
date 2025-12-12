<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();
        $admin = User::where('email', 'admin@example.com')->first();

        if ($users->isEmpty() || $projects->isEmpty()) {
            return;
        }

        $taskTemplates = [
            // Tasks cho dự án Phát triển Website
            [
                'title' => 'Thiết kế giao diện trang chủ',
                'description' => 'Thiết kế giao diện trang chủ với UI/UX hiện đại, responsive',
                'priority' => 'high',
                'status' => 'in_progress',
                'progress' => 60,
                'due_date' => now()->addDays(5),
            ],
            [
                'title' => 'Phát triển API Backend',
                'description' => 'Xây dựng các API endpoints cho hệ thống',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'progress' => 40,
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Tích hợp thanh toán',
                'description' => 'Tích hợp cổng thanh toán trực tuyến',
                'priority' => 'high',
                'status' => 'new',
                'progress' => 0,
                'due_date' => now()->addDays(10),
            ],
            [
                'title' => 'Kiểm thử chức năng',
                'description' => 'Kiểm thử toàn bộ chức năng của website',
                'priority' => 'medium',
                'status' => 'review',
                'progress' => 80,
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Tối ưu hiệu suất',
                'description' => 'Tối ưu tốc độ tải trang và hiệu suất hệ thống',
                'priority' => 'medium',
                'status' => 'new',
                'progress' => 0,
                'due_date' => now()->addDays(15),
            ],
            // Tasks cho dự án Nâng cấp Hệ thống
            [
                'title' => 'Phân tích yêu cầu nâng cấp',
                'description' => 'Phân tích và liệt kê các yêu cầu nâng cấp',
                'priority' => 'high',
                'status' => 'completed',
                'progress' => 100,
                'due_date' => now()->subDays(5),
                'completed_at' => now()->subDays(3),
            ],
            [
                'title' => 'Backup dữ liệu hiện tại',
                'description' => 'Sao lưu toàn bộ dữ liệu trước khi nâng cấp',
                'priority' => 'urgent',
                'status' => 'completed',
                'progress' => 100,
                'due_date' => now()->subDays(3),
                'completed_at' => now()->subDays(2),
            ],
            [
                'title' => 'Cài đặt phiên bản mới',
                'description' => 'Cài đặt và cấu hình phiên bản hệ thống mới',
                'priority' => 'high',
                'status' => 'in_progress',
                'progress' => 50,
                'due_date' => now()->addDays(2),
            ],
            [
                'title' => 'Migrate dữ liệu',
                'description' => 'Chuyển đổi dữ liệu sang định dạng mới',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'progress' => 30,
                'due_date' => now()->addDays(4),
            ],
            // Tasks cho dự án Marketing
            [
                'title' => 'Thiết kế banner quảng cáo',
                'description' => 'Thiết kế các banner quảng cáo cho chiến dịch',
                'priority' => 'high',
                'status' => 'completed',
                'progress' => 100,
                'due_date' => now()->subDays(10),
                'completed_at' => now()->subDays(8),
            ],
            [
                'title' => 'Viết nội dung bài viết',
                'description' => 'Viết nội dung bài viết cho blog và social media',
                'priority' => 'medium',
                'status' => 'in_progress',
                'progress' => 70,
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Chạy quảng cáo Facebook',
                'description' => 'Thiết lập và chạy quảng cáo trên Facebook',
                'priority' => 'high',
                'status' => 'in_progress',
                'progress' => 45,
                'due_date' => now()->addDays(5),
            ],
            // Tasks cho dự án Mở rộng Thị trường
            [
                'title' => 'Nghiên cứu thị trường',
                'description' => 'Nghiên cứu và phân tích các thị trường tiềm năng',
                'priority' => 'high',
                'status' => 'completed',
                'progress' => 100,
                'due_date' => now()->subDays(15),
                'completed_at' => now()->subDays(12),
            ],
            [
                'title' => 'Liên hệ đối tác',
                'description' => 'Liên hệ và đàm phán với các đối tác tiềm năng',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'progress' => 55,
                'due_date' => now()->addDays(2),
            ],
            [
                'title' => 'Mở văn phòng mới',
                'description' => 'Chuẩn bị và mở văn phòng tại địa điểm mới',
                'priority' => 'medium',
                'status' => 'planning',
                'progress' => 0,
                'due_date' => now()->addDays(30),
            ],
            // Tasks trễ hạn
            [
                'title' => 'Báo cáo tuần',
                'description' => 'Hoàn thành báo cáo công việc tuần trước',
                'priority' => 'medium',
                'status' => 'in_progress',
                'progress' => 20,
                'due_date' => now()->subDays(3),
            ],
            [
                'title' => 'Cập nhật tài liệu',
                'description' => 'Cập nhật tài liệu hướng dẫn sử dụng',
                'priority' => 'low',
                'status' => 'new',
                'progress' => 0,
                'due_date' => now()->subDays(1),
            ],
        ];

        $projectIndex = 0;
        $userIndex = 0;

        foreach ($taskTemplates as $taskData) {
            // Gán project (một số task không có project)
            $project = null;
            if ($projectIndex < $projects->count() - 2) {
                $project = $projects->get($projectIndex % min(4, $projects->count()));
            }

            // Gán assignee
            $assignee = $users->get($userIndex % $users->count());
            $userIndex++;

            // Gán creator (admin hoặc assignee)
            $creator = $admin ?? $assignee;

            Task::create([
                'project_id' => $project?->id,
                'creator_id' => $creator->id,
                'assignee_id' => $assignee->id,
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'priority' => $taskData['priority'],
                'status' => $taskData['status'],
                'progress' => $taskData['progress'],
                'due_date' => $taskData['due_date'],
                'completed_at' => $taskData['completed_at'] ?? null,
            ]);

            $projectIndex++;
        }

        // Tạo thêm một số tasks ngẫu nhiên
        for ($i = 0; $i < 10; $i++) {
            $project = $projects->random();
            $assignee = $users->random();
            $creator = $admin ?? $users->random();
            $priorities = ['urgent', 'high', 'medium', 'low'];
            $statuses = ['new', 'in_progress', 'review', 'completed'];
            $status = $statuses[array_rand($statuses)];

            $taskTitles = [
                'Phân tích yêu cầu',
                'Thiết kế giao diện',
                'Phát triển tính năng',
                'Kiểm thử hệ thống',
                'Tối ưu hiệu suất',
                'Cập nhật tài liệu',
                'Code review',
                'Fix bug',
                'Deploy lên production',
                'Họp với khách hàng',
            ];

            Task::create([
                'project_id' => $project->id,
                'creator_id' => $creator->id,
                'assignee_id' => $assignee->id,
                'title' => $taskTitles[$i % count($taskTitles)] . ' - ' . ($i + 1),
                'description' => 'Mô tả chi tiết cho công việc này. Cần hoàn thành đúng thời hạn và đảm bảo chất lượng.',
                'priority' => $priorities[array_rand($priorities)],
                'status' => $status,
                'progress' => $status === 'completed' ? 100 : rand(0, 90),
                'due_date' => now()->addDays(rand(-5, 30)),
                'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 10)) : null,
            ]);
        }
    }
}

