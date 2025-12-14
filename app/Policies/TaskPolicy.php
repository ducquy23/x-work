<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Chủ tịch và Ban điều hành xem tất cả
        if ($user->hasRole('chu-tich') || $user->hasRole('ban-dieu-hanh')) {
            return $user->can('view_any_task');
        }
        
        // Trưởng phòng, Giám đốc DA, Nhân viên đều có thể xem (sẽ filter trong query)
        return $user->can('view_any_task') || $user->can('view_task');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function view(User $user, Task $task): bool
    {
        // Chủ tịch và Ban điều hành xem tất cả
        if ($user->hasRole('chu-tich') || $user->hasRole('ban-dieu-hanh')) {
            return $user->can('view_task');
        }
        
        // Giám đốc DA xem công việc của dự án mình quản lý
        if ($user->hasRole('giam-doc-da')) {
            if ($task->project && $task->project->department_id === $user->department_id) {
                return $user->can('view_task');
            }
        }
        
        // Trưởng phòng xem công việc của phòng ban mình
        if ($user->hasRole('truong-phong')) {
            $canView = false;
            
            // Xem công việc được giao cho nhân viên trong phòng ban
            if ($task->assignees()->whereHas('department', function($q) use ($user) {
                $q->where('id', $user->department_id);
            })->exists()) {
                $canView = true;
            }
            
            // Xem công việc của dự án thuộc phòng ban
            if ($task->project && $task->project->department_id === $user->department_id) {
                $canView = true;
            }
            
            // Xem công việc được tạo bởi nhân viên trong phòng ban
            if ($task->creator && $task->creator->department_id === $user->department_id) {
                $canView = true;
            }
            
            return $canView && $user->can('view_task');
        }
        
        // Nhân viên chỉ xem công việc được giao cho mình hoặc mình tạo
        if ($user->hasRole('nhan-vien')) {
            $isAssigned = $task->assignees()->where('user_id', $user->id)->exists();
            $isCreator = $task->creator_id === $user->id;
            
            return ($isAssigned || $isCreator) && $user->can('view_task');
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Tất cả roles có quyền create_task đều có thể tạo công việc
        return $user->can('create_task');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function update(User $user, Task $task): bool
    {
        // Chủ tịch và Ban điều hành sửa tất cả
        if ($user->hasRole('chu-tich') || $user->hasRole('ban-dieu-hanh')) {
            return $user->can('update_task');
        }
        
        // Giám đốc DA sửa công việc của dự án mình quản lý
        if ($user->hasRole('giam-doc-da')) {
            if ($task->project && $task->project->department_id === $user->department_id) {
                return $user->can('update_task');
            }
        }
        
        // Trưởng phòng sửa công việc của phòng ban mình
        if ($user->hasRole('truong-phong')) {
            $canUpdate = false;
            
            // Sửa công việc được giao cho nhân viên trong phòng ban
            if ($task->assignees()->whereHas('department', function($q) use ($user) {
                $q->where('id', $user->department_id);
            })->exists()) {
                $canUpdate = true;
            }
            
            // Sửa công việc của dự án thuộc phòng ban
            if ($task->project && $task->project->department_id === $user->department_id) {
                $canUpdate = true;
            }
            
            return $canUpdate && $user->can('update_task');
        }
        
        // Nhân viên chỉ sửa công việc được giao cho mình hoặc mình tạo
        if ($user->hasRole('nhan-vien')) {
            $isAssigned = $task->assignees()->where('user_id', $user->id)->exists();
            $isCreator = $task->creator_id === $user->id;
            
            return ($isAssigned || $isCreator) && $user->can('update_task');
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function delete(User $user, Task $task): bool
    {
        // Chỉ Chủ tịch, Ban điều hành, Trưởng phòng và người tạo mới có thể xóa
        if ($user->hasRole('chu-tich') || $user->hasRole('ban-dieu-hanh')) {
            return $user->can('delete_task');
        }
        
        // Trưởng phòng xóa công việc của phòng ban mình
        if ($user->hasRole('truong-phong')) {
            if ($task->project && $task->project->department_id === $user->department_id) {
                return $user->can('delete_task');
            }
        }
        
        // Người tạo có thể xóa công việc mình tạo
        if ($task->creator_id === $user->id) {
            return $user->can('delete_task');
        }
        
        return false;
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_task');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->can('force_delete_task');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_task');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->can('restore_task');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_task');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function replicate(User $user, Task $task): bool
    {
        return $user->can('replicate_task');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_task');
    }

}
