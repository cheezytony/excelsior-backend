<?php

namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\Post\PostService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->successResponse($this->postService->getAll());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePostRequest  $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        return $this->dbTransaction(function () use ($request) {
            $post = $this->postService->create($request->validated());
            return $this->successResponse(
                compact('post'),
                statusCode: Response::HTTP_CREATED
            );
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  Post  $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        $post = $this->postService->get($post);
        return $this->successResponse(compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        return $this->dbTransaction(function () use ($request, $post) {
            $post = $this->postService->update($post, $request->validated());
            return $this->successResponse(compact('post'));
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        return $this->dbTransaction(function () use ($post) {
            $this->postService->delete($post);
            return $this->successResponse();
        });
    }
}
