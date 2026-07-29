<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Code;
use App\Models\Pack;
use App\Models\User;

class CodePolicy
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
    public function view(User $user, Code $code): bool
    {
        return $user->is($code->pack->user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Pack $pack): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Code $code, Pack $pack): bool
    {
        if ($code->sent_at === null) {
            return $code->pack->is($pack) && $user->is($code->pack->user);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Code $code, Pack $pack): bool
    {
        return $code->pack->is($pack)
            && $user->is($code->pack->user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Code $code): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Code $code): bool
    {
        return false;
    }
}
