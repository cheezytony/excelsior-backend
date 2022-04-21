<?php

namespace App\Http\Controllers\API\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Services\Authentication\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * Authentication service instance.
     *
     * @var AuthenticationService
     */
    public AuthenticationService $authenticationService;

    /**
     * Initialize controller.
     *
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        $this->middleware('auth:sanctum')->except(['store']);
    }

    /**
     * Authenticates a user with thr credentials provided.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $data = $this->authenticationService->login(
            $request->input("email"),
            $request->input("password")
        );

        return $this->successResponse($data, statusCode: Response::HTTP_CREATED);
    }

    /**
     * Refreshes a users authentication token.
     *
     * @return JsonResponse
     */
    public function update(): JsonResponse
    {
        return $this->successResponse($this->authenticationService->refreshToken());
    }

    public function destroy(): JsonResponse
    {
        $this->authenticationService->logout();
        return $this->successResponse();
    }
}
