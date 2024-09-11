<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name == RoleEnum::BOUTIQUIER->value;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->role->name == RoleEnum::BOUTIQUIER->value || $user->role->name == RoleEnum::CLIENT->value;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // dd($user->role->name == RoleEnum::BOUTIQUIER->value);
        return $user->role->name == RoleEnum::BOUTIQUIER->value;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->role->name == RoleEnum::BOUTIQUIER->value;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->role->name == RoleEnum::BOUTIQUIER->value;
    }
}
