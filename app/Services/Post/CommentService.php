<?php

namespace App\Services\Post;

use App\Http\Resources\CommentCollection;
use App\Models\Comment;
use App\Models\Post;

class CommentService
{
    /**
     * Paginate all posts.
     *
     * @param Post $post
     * @return CommentCollection
     */
    public function getAll(Post $post): CommentCollection
    {
        return new CommentCollection(
            $post->comments()
                ->whereDoesntHave('comment')
                ->withCount('comments')
                ->orderBy(
                    request('sort', 'created_at'),
                    request('order', 'desc')
                )
                ->paginate()
        );
    }
}
