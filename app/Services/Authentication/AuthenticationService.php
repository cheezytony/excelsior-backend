<?php

namespace App\Services\Authentication;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticationService
{
    /**
     * Attempts to authenticate a user using the credentials provided.
     *
     * @param string $email
     * @param string $password
     * @throws AuthenticationException
     * @return array<string, string>
     */
    public function login(string $email, string $password): array
    {
        $user = User::whereEmail($email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException();
        }

        return $this->authenticateUser($user);
    }

    public function logout(): void
    {
        $this->deleteTokens(request()->user());
    }

    /**
     * Creates and authenticates a new user.
     *
     * @param array<string, string> $params
     * @return array<string, string>
     */
    public function register(array $params): array
    {
        $user = User::create(array_merge($params, [
            'username' => $this->createUsername("{$params['first_name']} {$params['last_name']}"),
            'password' => Hash::make($params['password']),
        ]));
        $user->profile()->create();

        return $this->authenticateUser($user);
    }

    /**
     * Reauthenticates a user.
     *
     * @return array
     */
    public function refreshToken(): array
    {
        return $this->authenticateUser(request()->user());
    }

    /**
     * Authenticates the provided user.
     *
     * @param User $user
     * @return array<string, string>
     */
    public function authenticateUser(User $user): array
    {
        $this->deleteTokens($user);

        // Generate a new token.
        $token = $user->createToken('access-token')->plainTextToken;

        return [
            'user' => new UserResource($user),
            'token' => $token,
        ];
    }

    public function createUsername(string $name): string
    {
        $timesChecked = 0;
        $baseUsername = Str::slug($name);
        do {
            $username = $baseUsername;
            if ($timesChecked) {
                $username .= "-{$timesChecked}";
            }
            $timesChecked++;
            logger($username);
        } while (User::whereUsername($username)->exists());

        return $username;
    }

    public function deleteTokens(User $user): void
    {
        // Revoke all tokens.
        $user->tokens()->delete();
    }
}
