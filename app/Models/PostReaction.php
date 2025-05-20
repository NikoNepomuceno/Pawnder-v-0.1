<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property string $reaction_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User $user
 */
class PostReaction extends Model
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
        'reaction_type',
    ];

    /**
     * The valid reaction types.
     *
     * @var array<int, string>
     */
    public const REACTION_TYPES = [
        'like',
        'love',
        'sad',
        'angry',
        'wow',
    ];

    /**
     * Get the post that owns the reaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Post, \App\Models\PostReaction>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that owns the reaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\PostReaction>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the reaction type is valid.
     *
     * @param string $type
     * @return bool
     */
    public static function isValidReactionType(string $type): bool
    {
        return in_array($type, self::REACTION_TYPES);
    }
}
