<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem danh sách dịch vụ
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem chi tiết dịch vụ
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể tạo dịch vụ mới
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể cập nhật dịch vụ
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa dịch vụ
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể khôi phục dịch vụ đã xóa
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa vĩnh viễn dịch vụ
    }

    /**
     * Determine whether the user can activate/deactivate the service.
     */
    public function toggleStatus(User $user, Service $service): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể kích hoạt/vô hiệu hóa dịch vụ
    }
}
