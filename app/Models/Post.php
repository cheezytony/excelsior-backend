<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'body',
        'preview',
        'featured_image',
        'user_id',
        'topic_id',
    ];

    protected $with = [
        'tags', 'topic', 'user',
    ];

    protected array $filterableColumns = [
        'id', 'user_id', 'topic_id'
    ];

    protected array $searchableColumns = [
        'title', 'body'
    ];

    protected function featuredImage(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => !preg_match("/^http/", $value) && !empty($value)
                ? Storage::url($value)
                : $value,
        );
    }

    /**
     * Filter posts by the tags in the query.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeTags(Builder $query): void
    {
        if (request()->has('tags')) {
            $query->whereHas('tags', function ($tag) {
                $slugs = explode(',', request('tags'));
                $tag->whereIn('slug', $slugs);
            });
        }
    }

    /**
     * Get the comments of the post.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the tags of the post.
     *
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the topic of the post.
     *
     * @return BelongsTo
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the owner of the post.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
