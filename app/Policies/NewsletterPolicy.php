<?php

namespace App\Policies;

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsletterPolicy
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
    public function view(User $user, Newsletter $newsletter): bool
    {
        // Users can view newsletters they own, edit, or are recipients of
        return $newsletter->owner_id === $user->id || 
               $newsletter->users()->where('user_id', $user->id)->exists();
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
    public function update(User $user, Newsletter $newsletter): bool
    {
        // Only owners and editors can update newsletters
        if ($newsletter->owner_id === $user->id) {
            return true;
        }
        
        $role = $newsletter->users()->where('user_id', $user->id)->first()?->pivot->role;
        return $role === 'editor';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Newsletter $newsletter): bool
    {
        // Only owners can delete newsletters
        return $newsletter->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Newsletter $newsletter): bool
    {
        return $newsletter->owner_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Newsletter $newsletter): bool
    {
        return $newsletter->owner_id === $user->id;
    }

    /**
     * Determine whether the user can manage editors
     */
    public function manageEditors(User $user, Newsletter $newsletter): bool
    {
        return $newsletter->owner_id === $user->id;
    }

    /**
     * Determine whether the user can manage recipients
     */
    public function manageRecipients(User $user, Newsletter $newsletter): bool
    {
        // Both owners and editors can manage recipients
        if ($newsletter->owner_id === $user->id) {
            return true;
        }
        
        $role = $newsletter->users()->where('user_id', $user->id)->first()?->pivot->role;
        return $role === 'editor';
    }
}
