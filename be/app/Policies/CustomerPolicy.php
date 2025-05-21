<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem danh sách khách hàng
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem chi tiết khách hàng
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể tạo khách hàng mới
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể cập nhật thông tin khách hàng
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa khách hàng
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể khôi phục khách hàng đã xóa
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa vĩnh viễn khách hàng
    }

    /**
     * Determine whether the user can view customer's contracts.
     */
    public function viewContracts(User $user, Customer $customer): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem hợp đồng của khách hàng
    }

    /**
     * Determine whether the user can view customer's invoices.
     */
    public function viewInvoices(User $user, Customer $customer): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem hóa đơn của khách hàng
    }
}
