<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'email_verified_at',
        'profile_picture',
        'banner_image',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the user's profile picture URL with fallback
     *
     * @return string
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if (empty($this->profile_picture)) {
            return asset('images/default-profile.png');
        }

        // If it's a relative path, make it absolute
        if (!str_starts_with($this->profile_picture, 'http')) {
            return asset($this->profile_picture);
        }

        return $this->profile_picture;
    }

    /**
     * Check if the user has a custom (non-Google) profile picture
     *
     * @return bool
     */
    public function hasCustomProfilePicture(): bool
    {
        return !empty($this->profile_picture) &&
               !str_contains($this->profile_picture, 'googleusercontent.com') &&
               !str_contains($this->profile_picture, 'profile_pictures/google_');
    }

    /**
     * Get the user's initials for fallback display
     *
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        $firstInitial = !empty($this->first_name) ? strtoupper($this->first_name[0]) : '';
        $lastInitial = !empty($this->last_name) ? strtoupper($this->last_name[0]) : '';

        return $firstInitial . $lastInitial;
    }
}
