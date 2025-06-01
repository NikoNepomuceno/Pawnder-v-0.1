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
        'violation_reasons',
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
        'violation_reasons' => 'array',
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
     * The valid report reasons (legacy).
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
     * The new violation categories.
     *
     * @var array<int, string>
     */
    public const VIOLATION_CATEGORIES = [
        // Content-Related Violations
        'sexual_content',
        'violence',
        'hate_speech',
        'harassment',
        'self_harm',
        'spam',
        'misinformation',
        'abusive_language',
        'terrorism',
        'illegal_activities',
        // User Behavior Violations
        'impersonation',
        'stalking',
        'inappropriate_messages',
        'unwanted_contact',
        'scam',
        // Platform/Community Violations
        'community_guidelines',
        'copyright',
        'inappropriate_profile',
    ];

    /**
     * Human-readable labels for violation categories.
     *
     * @var array<string, string>
     */
    public const VIOLATION_LABELS = [
        // Content-Related Violations
        'sexual_content' => 'Sexual content or nudity',
        'violence' => 'Violence or graphic content',
        'hate_speech' => 'Hate speech or symbols',
        'harassment' => 'Harassment or bullying',
        'self_harm' => 'Self-harm or suicide promotion',
        'spam' => 'Spam or misleading information',
        'misinformation' => 'Misinformation or false claims',
        'abusive_language' => 'Foul or abusive language',
        'terrorism' => 'Terrorism or violent extremism',
        'illegal_activities' => 'Illegal activities (e.g. drug use, weapons)',
        // User Behavior Violations
        'impersonation' => 'Impersonation or fake account',
        'stalking' => 'Stalking or threatening behavior',
        'inappropriate_messages' => 'Inappropriate direct messages',
        'unwanted_contact' => 'Unwanted contact',
        'scam' => 'Scam or phishing',
        // Platform/Community Violations
        'community_guidelines' => 'Violation of community guidelines',
        'copyright' => 'Copyright or intellectual property infringement',
        'inappropriate_profile' => 'Inappropriate username or profile',
    ];

    /**
     * Categorized violation types for admin display.
     *
     * @var array<string, array<string>>
     */
    public const VIOLATION_CATEGORIES_GROUPED = [
        'Content-Related Violations' => [
            'sexual_content',
            'violence',
            'hate_speech',
            'harassment',
            'self_harm',
            'spam',
            'misinformation',
            'abusive_language',
            'terrorism',
            'illegal_activities',
        ],
        'User Behavior Violations' => [
            'impersonation',
            'stalking',
            'inappropriate_messages',
            'unwanted_contact',
            'scam',
        ],
        'Platform/Community Violations' => [
            'community_guidelines',
            'copyright',
            'inappropriate_profile',
        ],
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

    /**
     * Get formatted violation reasons for display.
     *
     * @return string
     */
    public function getFormattedViolationReasonsAttribute(): string
    {
        if (empty($this->violation_reasons)) {
            return $this->reason ?? 'No reason provided';
        }

        $labels = [];
        foreach ($this->violation_reasons as $reason) {
            $labels[] = self::VIOLATION_LABELS[$reason] ?? $reason;
        }

        return implode(', ', $labels);
    }

    /**
     * Get violation reasons grouped by category for admin display.
     *
     * @return array<string, array<string>>
     */
    public function getGroupedViolationReasonsAttribute(): array
    {
        if (empty($this->violation_reasons)) {
            return [];
        }

        $grouped = [];
        foreach (self::VIOLATION_CATEGORIES_GROUPED as $category => $categoryReasons) {
            $matchingReasons = array_intersect($this->violation_reasons, $categoryReasons);
            if (!empty($matchingReasons)) {
                $grouped[$category] = array_map(function($reason) {
                    return self::VIOLATION_LABELS[$reason] ?? $reason;
                }, $matchingReasons);
            }
        }

        return $grouped;
    }

    /**
     * Check if this report uses the new violation categories system.
     *
     * @return bool
     */
    public function hasViolationCategories(): bool
    {
        return !empty($this->violation_reasons) && is_array($this->violation_reasons);
    }
}
