<?php

namespace App\Services\User;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserService
{
    /**
     * Get a single user wrapped in the user resource class.
     *
     * @param User $user
     * @return UserResource
     */
    public function get(User $user): UserResource
    {
        $user->loadCount(['posts', 'comments']);
        return new UserResource($user);
    }
}
