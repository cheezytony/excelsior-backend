<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\User;
use App\Services\Post\PostService;

class UserPostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return $this->paginatedResponse(
            $this->postService->getUserPosts($user),
            PostResource::class
        );
    }
}
