<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
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
        // Tất cả users đều có thể xem danh sách phòng ban
        return $user->can('view_any_department') || $user->can('view_department');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return bool
     */
    public function view(User $user, Department $department): bool
    {
        // Chủ tịch và Ban điều hành xem tất cả
        if ($user->hasRole('chu-tich') || $user->hasRole('ban-dieu-hanh')) {
            return $user->can('view_department');
        }
        
        // Các roles khác chỉ xem phòng ban của mình
        return $user->department_id === $department->id && $user->can('view_department');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Chỉ Chủ tịch và Ban điều hành mới có thể tạo phòng ban
        return ($user->hasRole('chu-tich') || $user->hasRole('ban-dieu-hanh')) 
            && $user->can('create_department');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return bool
     */
    public function update(User $user, Department $department): bool
    {
        // Chỉ Chủ tịch và Ban điều hành mới có thể sửa phòng ban
        return ($user->hasRole('chu-tich') || $user->hasRole('ban-dieu-hanh')) 
            && $user->can('update_department');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return bool
     */
    public function delete(User $user, Department $department): bool
    {
        // Chỉ Chủ tịch mới có thể xóa phòng ban
        return $user->hasRole('chu-tich') && $user->can('delete_department');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_department');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return bool
     */
    public function forceDelete(User $user, Department $department): bool
    {
        return $user->can('force_delete_department');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_department');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return bool
     */
    public function restore(User $user, Department $department): bool
    {
        return $user->can('restore_department');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_department');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return bool
     */
    public function replicate(User $user, Department $department): bool
    {
        return $user->can('replicate_department');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_department');
    }

}
