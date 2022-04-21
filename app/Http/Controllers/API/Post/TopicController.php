<?php

namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Services\Post\TopicService;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    protected TopicService $topicService;

    public function __construct(TopicService $topicService)
    {
        $this->topicService = $topicService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->successResponse(
            $this->topicService->getAll()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show(Topic $topic)
    {
        return $this->successResponse([
            'topic' => $this->topicService->get($topic)
        ]);
    }
}
