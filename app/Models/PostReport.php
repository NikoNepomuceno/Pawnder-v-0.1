<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_id
 * @property int $reported_by
 * @property string $reason
 * @property string $status
 * @property \Carbon\Carbon|null $reviewed_at
 * @property int|null $reviewed_by
 * @property string|null $admin_notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User $reporter
 * @property-read \App\Models\User|null $reviewer
 */
class PostReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'reported_by',
        'reason',
        'status',
        'reviewed_at',
        'reviewed_by',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * The valid report statuses.
     *
     * @var array<int, string>
     */
    public const STATUSES = [
        'pending',
        'reviewing',
        'resolved',
        'dismissed',
    ];

    /**
     * The valid report reasons.
     *
     * @var array<int, string>
     */
    public const REASONS = [
        'inappropriate_content',
        'spam',
        'harassment',
        'fake_post',
        'other',
    ];

    /**
     * Get the post that was reported.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Post, \App\Models\PostReport>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user who reported the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\PostReport>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the admin who reviewed the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\PostReport>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if the report status is valid.
     *
     * @param string $status
     * @return bool
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::STATUSES);
    }

    /**
     * Check if the report reason is valid.
     *
     * @param string $reason
     * @return bool
     */
    public static function isValidReason(string $reason): bool
    {
        return in_array($reason, self::REASONS);
    }

    /**
     * Check if the report has been reviewed.
     *
     * @return bool
     */
    public function isReviewed(): bool
    {
        return !is_null($this->reviewed_at);
    }
}
