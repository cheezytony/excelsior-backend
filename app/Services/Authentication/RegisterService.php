<?php

namespace App\Services\Authentication;

use App\Models\User;

class RegisterService
{
    /**
     * @var AuthenticationService
     */
    protected AuthenticationService $authenticationService;

    /**
     * Initialize service class.
     *
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->$authenticationService = $authenticationService;
    }

    /**
     * Creates and authenticates a new user.
     *
     * @param array $params
     * @return array<string, string>
     */
    public function register(array $params): array
    {
        $user = User::create($params);

        return $this->authenticationService->authenticateUser($user);
    }
}
