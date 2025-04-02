<?php

namespace App\Policies;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read permissions');
    }
    /**
     * Determine whether the user can view the model.
     */

    public function view(User $user): bool
    {
        return $user->can('read permissions');
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create permissions');
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->can('update permissions');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->can('delete permissions');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return false;
    }
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return false;
    }
}
