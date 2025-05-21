<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem danh sách phòng
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Room $room): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem chi tiết phòng
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể tạo phòng mới
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Room $room): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể cập nhật phòng
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Room $room): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa phòng
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Room $room): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể khôi phục phòng đã xóa
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Room $room): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa vĩnh viễn phòng
    }
}
