<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $breed
 * @property string $location
 * @property string $contact
 * @property array $photo_urls
 * @property bool $is_flagged
 * @property string|null $flag_reason
 * @property int|null $shared_post_id
 * @property int $share_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostComment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostComment[] $allComments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostReaction[] $reactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostReport[] $reports
 * @property-read array $reaction_counts
 * @property-read string|null $current_user_reaction
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostShare[] $shares
 * @property-read \App\Models\Post|null $originalPost
 * @property-read \App\Models\User $sharedBy
 */
class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'breed',
        'location',
        'contact',
        'photo_urls',
        'is_flagged',
        'flag_reason',
        'shared_post_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'photo_urls' => 'json',
        'is_flagged' => 'boolean',
        'share_count' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'reaction_counts',
        'current_user_reaction',
    ];

    /**
     * Get the user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Post>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the post (top-level comments only).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PostComment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_id');
    }

    /**
     * Get all comments (including replies) for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PostComment>
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    /**
     * Get the reactions for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PostReaction>
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    /**
     * Get the reports for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PostReport>
     */
    public function reports(): HasMany
    {
        return $this->hasMany(PostReport::class);
    }

    /**
     * Get reaction counts by type.
     *
     * @return array<string, int>
     */
    public function getReactionCountsAttribute(): array
    {
        return $this->reactions()
            ->selectRaw('reaction_type, count(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();
    }

    /**
     * Get the current user's reaction to this post.
     *
     * @return string|null
     */
    public function getCurrentUserReactionAttribute(): ?string
    {
        if (!Auth::check()) {
            return null;
        }

        return $this->reactions()
            ->where('user_id', Auth::id())
            ->value('reaction_type');
    }

    /**
     * Get the shares for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\PostShare>
     */
    public function shares(): HasMany
    {
        return $this->hasMany(PostShare::class);
    }

    /**
     * If this post is a share, get the original post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Post, \App\Models\Post>
     */
    public function originalPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'shared_post_id');
    }

    /**
     * If this post is a share, get the user who shared it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Post>
     */
    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper to check if this post is a share.
     *
     * @return bool
     */
    public function isShared(): bool
    {
        return !is_null($this->shared_post_id);
    }
}
