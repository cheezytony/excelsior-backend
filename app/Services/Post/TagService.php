<?php

namespace App\Services\Post;

use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class TagService
{
    /**
     * Paginate all tags.
     *
     * @return TagCollection
     */
    public function getAll(): TagCollection
    {
        return new TagCollection(
            Tag::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->paginate(request('limit'))
        );
    }

    /**
     * Paginate all tags.
     *
     * @param Tag $tag
     * @return TagResource
     */
    public function get(Tag $tag): TagResource
    {
        $tag->loadCount('posts');
        return new TagResource($tag);
    }
}
