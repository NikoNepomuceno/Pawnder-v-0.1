<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property string $content
 * @property int|null $parent_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User $user
 * @property-read \App\Models\PostComment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostComment[] $replies
 */
class PostComment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'content',
        'parent_id',
    ];

    /**
     * Get the post that owns the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Post, \App\Models\PostComment>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that owns the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\PostComment>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\PostComment, \App\Models\PostComment>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PostComment>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }

    /**
     * Check if the comment is a reply.
     *
     * @return bool
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }
}
