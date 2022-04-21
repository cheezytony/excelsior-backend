<?php

namespace App\Http\Controllers\API\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Services\Authentication\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
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
    }

    /**
     * Creates a new user with the parameters provided in the request.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        return $this->dbTransaction(function () use ($request) {
            $data = $this->authenticationService->register($request->validated());
            return $this->successResponse($data, statusCode: Response::HTTP_CREATED);
        });
    }
}
