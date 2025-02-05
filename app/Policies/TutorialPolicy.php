<?php

namespace App\Policies;

use App\Models\Tutorial;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TutorialPolicy
{
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
    public function view(User $user, Tutorial $tutorial): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('moderator');

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tutorial $tutorial): bool
    {
        return $user->hasRole('admin') || $user->hasRole('moderator');

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tutorial $tutorial): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tutorial $tutorial): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tutorial $tutorial): bool
    {
        return $user->hasRole('admin');

    }
}
