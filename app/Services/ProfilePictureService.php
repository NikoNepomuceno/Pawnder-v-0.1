<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use GuzzleHttp\Exception\RequestException;

class ProfilePictureService
{
    /**
     * Process and store a profile picture from Google OAuth
     *
     * @param string $googleAvatarUrl
     * @param string $userId
     * @param string|null $existingProfilePicture
     * @return string|null
     */
    public function processGoogleProfilePicture(string $googleAvatarUrl, string $userId, ?string $existingProfilePicture = null): ?string
    {
        try {
            // If user already has a custom profile picture, don't override it
            if ($existingProfilePicture && !$this->isGoogleProfilePicture($existingProfilePicture)) {
                Log::info('Preserving existing custom profile picture for user', ['user_id' => $userId]);
                return $existingProfilePicture;
            }

            // Download and process the Google profile picture
            return $this->downloadAndStoreProfilePicture($googleAvatarUrl, $userId);

        } catch (\Exception $e) {
            Log::error('Failed to process Google profile picture', [
                'user_id' => $userId,
                'google_url' => $googleAvatarUrl,
                'error' => $e->getMessage()
            ]);
            
            // Return existing picture or null if processing fails
            return $existingProfilePicture;
        }
    }

    /**
     * Download profile picture from Google and store it in Cloudinary
     *
     * @param string $googleAvatarUrl
     * @param string $userId
     * @return string|null
     */
    private function downloadAndStoreProfilePicture(string $googleAvatarUrl, string $userId): ?string
    {
        try {
            // Get higher resolution version of Google profile picture
            $highResUrl = $this->getHighResolutionGoogleAvatar($googleAvatarUrl);
            
            // Download the image
            $response = Http::timeout(10)->get($highResUrl);
            
            if (!$response->successful()) {
                throw new \Exception('Failed to download profile picture from Google');
            }

            $imageData = $response->body();
            $contentType = $response->header('Content-Type');
            
            // Validate image type
            if (!$this->isValidImageType($contentType)) {
                throw new \Exception('Invalid image type: ' . $contentType);
            }

            // Create a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'google_profile_' . $userId);
            file_put_contents($tempFile, $imageData);

            try {
                // Upload to Cloudinary with transformations
                $uploadResult = Cloudinary::upload($tempFile, [
                    'public_id' => 'profile_pictures/google_' . $userId . '_' . time(),
                    'transformation' => [
                        'width' => 400,
                        'height' => 400,
                        'crop' => 'fill',
                        'gravity' => 'face',
                        'quality' => 'auto:good',
                        'format' => 'webp'
                    ],
                    'tags' => ['profile_picture', 'google_oauth']
                ]);

                return $uploadResult->getSecurePath();

            } finally {
                // Clean up temporary file
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
            }

        } catch (RequestException $e) {
            Log::error('HTTP error downloading Google profile picture', [
                'user_id' => $userId,
                'url' => $googleAvatarUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error storing Google profile picture', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get higher resolution version of Google avatar URL
     *
     * @param string $googleAvatarUrl
     * @return string
     */
    private function getHighResolutionGoogleAvatar(string $googleAvatarUrl): string
    {
        // Google profile pictures can be requested in higher resolution
        // by modifying the size parameter in the URL
        return preg_replace('/=s\d+/', '=s400', $googleAvatarUrl);
    }

    /**
     * Check if the profile picture URL is from Google
     *
     * @param string $profilePictureUrl
     * @return bool
     */
    private function isGoogleProfilePicture(string $profilePictureUrl): bool
    {
        return str_contains($profilePictureUrl, 'googleusercontent.com') ||
               str_contains($profilePictureUrl, 'profile_pictures/google_');
    }

    /**
     * Validate image content type
     *
     * @param string|null $contentType
     * @return bool
     */
    private function isValidImageType(?string $contentType): bool
    {
        $allowedTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'image/gif'
        ];

        return in_array($contentType, $allowedTypes);
    }

    /**
     * Get fallback profile picture URL
     *
     * @return string
     */
    public function getFallbackProfilePicture(): string
    {
        return asset('images/default-profile.png');
    }

    /**
     * Generate profile picture URL with fallback
     *
     * @param string|null $profilePicture
     * @return string
     */
    public function getProfilePictureUrl(?string $profilePicture): string
    {
        if (empty($profilePicture)) {
            return $this->getFallbackProfilePicture();
        }

        // If it's a relative path, make it absolute
        if (!str_starts_with($profilePicture, 'http')) {
            return asset($profilePicture);
        }

        return $profilePicture;
    }

    /**
     * Delete profile picture from Cloudinary
     *
     * @param string $profilePictureUrl
     * @return bool
     */
    public function deleteProfilePicture(string $profilePictureUrl): bool
    {
        try {
            if ($this->isGoogleProfilePicture($profilePictureUrl)) {
                $urlParts = parse_url($profilePictureUrl);
                if ($urlParts && isset($urlParts['path'])) {
                    $pathParts = pathinfo($urlParts['path']);
                    if (isset($pathParts['filename'])) {
                        $publicId = $pathParts['filename'];
                        Cloudinary::destroy($publicId);
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete profile picture from Cloudinary', [
                'url' => $profilePictureUrl,
                'error' => $e->getMessage()
            ]);
        }

        return false;
    }
}
