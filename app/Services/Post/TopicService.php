<?php

namespace App\Services\Post;

use App\Http\Resources\TopicCollection;
use App\Http\Resources\TopicResource;
use App\Models\Topic;

class TopicService
{
    /**
     * Paginate all topics.
     *
     * @return TopicCollection
     */
    public function getAll(): TopicCollection
    {
        return new TopicCollection(
            Topic::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->paginate(request('limit'))
        );
    }

    /**
     * Paginate all topics.
     *
     * @param Topic $topic
     * @return TopicResource
     */
    public function get(Topic $topic): TopicResource
    {
        $topic->loadCount('posts');
        return new TopicResource($topic);
    }
}
