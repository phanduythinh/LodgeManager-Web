<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contract $contract): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return $contract->status !== 'inactive';
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contract $contract): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $contract->status !== 'inactive' && !$contract->isExpired();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contract $contract): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $contract->status === 'inactive' && !$contract->hasActiveInvoices();
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contract $contract): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contract $contract): bool
    {
        return $user->hasRole('admin') && !$contract->hasActiveInvoices();
    }

    /**
     * Determine whether the user can manage contract payments.
     */
    public function managePayments(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage contract services.
     */
    public function manageServices(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view contract history.
     */
    public function viewHistory(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage contract documents.
     */
    public function manageDocuments(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can terminate the contract.
     */
    public function terminate(User $user, Contract $contract): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return !$contract->isExpired() && !$contract->isTerminated();
        }

        return false;
    }

    /**
     * Determine whether the user can renew the contract.
     */
    public function renew(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) && $contract->isExpiringSoon();
    }
}
