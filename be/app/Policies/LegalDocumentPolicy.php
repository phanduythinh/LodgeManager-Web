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
        return $user->hasAnyRole(['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegalDocument $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return $document->status !== 'inactive';
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
    public function update(User $user, LegalDocument $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $document->status !== 'inactive';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LegalDocument $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $document->status === 'inactive' && !$document->isReferenced();
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LegalDocument $document): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LegalDocument $document): bool
    {
        return $user->hasRole('admin') && !$document->isReferenced();
    }

    /**
     * Determine whether the user can manage document versions.
     */
    public function manageVersions(User $user, LegalDocument $document): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view document history.
     */
    public function viewHistory(User $user, LegalDocument $document): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage document templates.
     */
    public function manageTemplates(User $user, LegalDocument $document): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage document categories.
     */
    public function manageCategories(User $user, LegalDocument $document): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage document permissions.
     */
    public function managePermissions(User $user, LegalDocument $document): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage document workflows.
     */
    public function manageWorkflows(User $user, LegalDocument $document): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, LegalDocument $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return $document->status !== 'inactive';
        }

        return false;
    }

    /**
     * Determine whether the user can verify the document.
     */
    public function verify(User $user, LegalDocument $document): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $document->status !== 'inactive';
        }

        return false;
    }
}
