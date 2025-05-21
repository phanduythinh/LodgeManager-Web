<?php

namespace App\Policies;

use App\Models\LegalDocument;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LegalDocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem danh sách tài liệu pháp lý
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegalDocument $legalDocument): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể xem chi tiết tài liệu pháp lý
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể tạo tài liệu pháp lý mới
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LegalDocument $legalDocument): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể cập nhật tài liệu pháp lý
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LegalDocument $legalDocument): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa tài liệu pháp lý
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LegalDocument $legalDocument): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể khôi phục tài liệu pháp lý đã xóa
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LegalDocument $legalDocument): bool
    {
        return $user->hasRole('admin'); // Chỉ admin có thể xóa vĩnh viễn tài liệu pháp lý
    }

    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, LegalDocument $legalDocument): bool
    {
        return true; // Tất cả người dùng đã đăng nhập có thể tải xuống tài liệu pháp lý
    }

    /**
     * Determine whether the user can verify the document.
     */
    public function verify(User $user, LegalDocument $legalDocument): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager'); // Chỉ admin và manager có thể xác minh tài liệu pháp lý
    }
}
