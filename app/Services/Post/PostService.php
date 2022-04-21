<?php

namespace App\Services\Post;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    /**
     * Paginate all posts.
     *
     * @return PostCollection
     */
    public function getAll(): PostCollection
    {
        return new PostCollection(
            Post::search()
                ->filter()
                ->tags()
                ->withCount('comments')
                ->orderBy(
                    request('sort', 'created_at'),
                    request('order', 'desc')
                )
                ->paginate()
        );
    }

    /**
     * Returns a single post wrapped in a post resource class.
     *
     * @param Post $post
     * @return PostResource
     */
    public function get(Post $post): PostResource
    {
        $post->loadCount('comments');
        return new PostResource($post);
    }

    /**
     * Returns a query builder instance of the user's posts.
     *
     * @param User $user
     * @return Builder
     */
    public function getUserPosts(User $user): Builder
    {
        return $user->posts();
    }

    /**
     * Creates and returns a new post.
     *
     * @param array $params
     * @return PostResource
     */
    public function create(array $params): PostResource
    {
        $path = optional($params['featured_image'])->store('featured_images') ?: null;

        $topic = Topic::whereTitle($params['topic'])->first();

        $post = Post::create([
            'user_id' => auth()->user()->id,
            'title' => $params['title'],
            'body' => $params['body'],
            'preview' => $params['preview'],
            'featured_image' => $path,
            'slug' => Str::slug($params['title']),
            'topic_id' => $topic->id,
        ]);

        $this->attachPostTags($post, $params['tags']);

        return new PostResource($post);
    }

    /**
     * Updates a given post with the params provided.
     *
     * @param Post $post
     * @param array $params
     * @return PostResource
     */
    public function update(Post $post, array $params): PostResource
    {
        $post->update([
            'title' => $params['title'],
            'body' => $params['body'],
        ]);

        $post->tags()->detach();

        $this->attachPostTags($post, $params['tags']);

        return new PostResource($post);
    }

    /**
     * Deletes a post and its dependents
     *
     * @param Post $post
     * @return void
     */
    public function delete(Post $post): void
    {
        // Unlink all tags.
        $post->tags()->detach();

        // Delete all comments.
        $post->comments()->delete();

        // Delete post.
        $post->delete();
    }

    /**
     * Attaches the tags provided to the post specified.
     *
     * @param Post $post
     * @param array $slugs
     * @return void
     */
    public function attachPostTags(Post $post, array $slugs): void
    {
        foreach ($slugs as $slug) {
            $tag = Tag::firstOrNew([
                'title' => $slug,
                'slug' => Str::slug($slug),
            ]);

            $post->tags()->save($tag);
        }

        $post->load('tags');
    }
}
