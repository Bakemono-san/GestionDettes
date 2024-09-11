<?php
namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class ArticlePolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Check if the user is an ADMIN or BOUTIQUIER
        return $user->role->name == RoleEnum::ADMIN->value || $user->role->name == RoleEnum::BOUTIQUIER->value;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        // Check if the user is a BOUTIQUIER->value or ADMIN
        // return $user->role->name == RoleEnum::BOUTIQUIER->value || $user->role->name == RoleEnum::ADMIN->value;
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Check if the user is a BOUTIQUIER->value or ADMIN
        return $user->role->name == RoleEnum::BOUTIQUIER->value || $user->role->name == RoleEnum::ADMIN->value;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        // Check if the user is a BOUTIQUIER->value or ADMIN
        return $user->role->name == RoleEnum::BOUTIQUIER->value || $user->role->name == RoleEnum::ADMIN->value;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        // Check if the user is an ADMIN
        return $user->role->name == RoleEnum::ADMIN->value;
    }
}
