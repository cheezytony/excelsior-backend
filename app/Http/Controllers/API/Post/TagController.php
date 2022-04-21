<?php

namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\Post\TagService;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            $this->tagService->getAll(),
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag): JsonResponse
    {
        return $this->successResponse([
            'tag' => $this->tagService->get($tag),
        ]);
    }
}
