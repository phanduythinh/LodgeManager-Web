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
        return $user->hasAnyRole(['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Room $room): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return $room->status !== 'inactive';
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
    public function update(User $user, Room $room): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $room->status !== 'inactive';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Room $room): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $room->status === 'inactive' && !$room->hasActiveContract();
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Room $room): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Room $room): bool
    {
        return $user->hasRole('admin') && !$room->hasActiveContract();
    }

    /**
     * Determine whether the user can manage room contracts.
     */
    public function manageContracts(User $user, Room $room): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage room maintenance.
     */
    public function manageMaintenance(User $user, Room $room): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can view room history.
     */
    public function viewHistory(User $user, Room $room): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can manage room inventory.
     */
    public function manageInventory(User $user, Room $room): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'staff']);
    }

    /**
     * Determine whether the user can manage room services.
     */
    public function manageServices(User $user, Room $room): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
