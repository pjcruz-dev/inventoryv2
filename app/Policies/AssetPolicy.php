<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('view_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('edit_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('delete_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can assign assets.
     */
    public function assign(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('assign_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can unassign assets.
     */
    public function unassign(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('assign_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can reassign assets.
     */
    public function reassign(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('assign_assets') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can send assets to maintenance.
     */
    public function maintenance(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('manage_maintenance') || $user->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Determine whether the user can send assets to disposal.
     */
    public function dispose(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('manage_disposals') || $user->hasRole(['Admin', 'Super Admin']);
    }
}