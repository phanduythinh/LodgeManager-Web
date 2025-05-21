<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem danh sách báo cáo
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem chi tiết báo cáo
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể tạo báo cáo mới
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Report $report): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể cập nhật báo cáo
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Report $report): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa báo cáo
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Report $report): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể khôi phục báo cáo đã xóa
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Report $report): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa vĩnh viễn báo cáo
    }

    /**
     * Determine whether the user can publish the report.
     */
    public function publish(User $user, Report $report): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể xuất bản báo cáo
    }

    /**
     * Determine whether the user can archive the report.
     */
    public function archive(User $user, Report $report): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể lưu trữ báo cáo
    }

    /**
     * Determine whether the user can export the report.
     */
    public function export(User $user, Report $report): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xuất báo cáo
    }
}
