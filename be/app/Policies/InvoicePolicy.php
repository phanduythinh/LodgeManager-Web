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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage invoice payments.
     */
    public function managePayments(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view invoice history.
     */
    public function viewHistory(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage invoice documents.
     */
    public function manageDocuments(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can cancel the invoice.
     */
    public function cancel(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $invoice->status === 'pending' || $invoice->status === 'draft';
        }

        return false;
    }

    /**
     * Determine whether the user can void the invoice.
     */
    public function void(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') && $invoice->status === 'paid';
    }

    /**
     * Determine whether the user can send payment reminders.
     */
    public function sendReminders(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) && $invoice->status === 'pending';
    }

    /**
     * Determine whether the user can apply late payment fees.
     */
    public function applyLateFees(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) && $invoice->status === 'overdue';
    }

    /**
     * Determine whether the user can mark the invoice as paid.
     */
    public function markAsPaid(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }
}
