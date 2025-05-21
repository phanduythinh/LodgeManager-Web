<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem danh sách hóa đơn
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem chi tiết hóa đơn
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể tạo hóa đơn mới
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể cập nhật hóa đơn
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa hóa đơn
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể khôi phục hóa đơn đã xóa
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa vĩnh viễn hóa đơn
    }

    /**
     * Determine whether the user can mark invoice as paid.
     */
    public function markAsPaid(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể đánh dấu hóa đơn đã thanh toán
    }

    /**
     * Determine whether the user can cancel invoice.
     */
    public function cancel(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể hủy hóa đơn
    }
}
