<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
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
    public function view(User $user, Publication $publication): bool
    {
        // Owners and any team member may view the publication.
        return $publication->owner_id === $user->id
            || $publication->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Publication $publication): bool
    {
        // Owners and editors can manage the publication's content.
        return $this->hasRole($user, $publication, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Publication $publication): bool
    {
        return $publication->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Publication $publication): bool
    {
        return $publication->owner_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Publication $publication): bool
    {
        return $publication->owner_id === $user->id;
    }

    /**
     * Determine whether the user can manage the publication's team.
     */
    public function manageEditors(User $user, Publication $publication): bool
    {
        return $publication->owner_id === $user->id;
    }

    /**
     * Determine whether the user holds one of the given roles on the publication.
     */
    protected function hasRole(User $user, Publication $publication, array $roles): bool
    {
        if ($publication->owner_id === $user->id) {
            return true;
        }

        $role = $publication->members()->where('user_id', $user->id)->first()?->pivot->role;

        return in_array($role, $roles, true);
    }
}
