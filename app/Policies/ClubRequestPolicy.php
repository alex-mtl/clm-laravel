<?php

namespace App\Policies;

use App\Models\ClubRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClubRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClubRequest $clubRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClubRequest $clubRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClubRequest $clubRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClubRequest $clubRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClubRequest $clubRequest): bool
    {
        return false;
    }

    public function approve(User $user, ClubRequest $request)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return $user->id === $request->club->owner_id;
    }

    public function decline(User $user, ClubRequest $request)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        // Typically same logic as approve
        return $user->id === $request->club->owner_id;
    }
}
