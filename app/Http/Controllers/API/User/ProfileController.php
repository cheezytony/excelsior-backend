<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Services\User\ProfileService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * @var ProfileService
     */
    protected ProfileService $profileService;

    /**
     * Initialize service class.
     *
     * @param ProfileService $profileService
     */
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
        $this->middleware('auth:sanctum');
    }

    public function index(): JsonResponse
    {
        return $this->successResponse([
            'user' => $this->profileService->getAuthenticatedUser(),
        ]);
    }
}
