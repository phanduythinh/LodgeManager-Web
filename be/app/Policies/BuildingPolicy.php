<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuildingPolicy
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
    public function view(User $user, Building $building): bool
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
    public function update(User $user, Building $building): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Building $building): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Building $building): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Building $building): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage building services.
     */
    public function manageServices(User $user, Building $building): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage building maintenance.
     */
    public function manageMaintenance(User $user, Building $building): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can view building reports.
     */
    public function viewReports(User $user, Building $building): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage building documents.
     */
    public function manageDocuments(User $user, Building $building): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
