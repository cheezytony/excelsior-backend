<?php

namespace App\Services\User;

use App\Http\Resources\UserResource;
use App\Models\User;

class ProfileService
{
    /**
     * Get a single user wrapped in the user resource class.
     *
     * @return UserResource
     */
    public function getAuthenticatedUser(): UserResource
    {
        return new UserResource(User::withCount('posts', 'comments')->find(auth()->id()));
    }
}
