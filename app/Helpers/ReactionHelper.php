<?php

namespace App\Helpers;

class ReactionHelper
{
    /**
     * Get the Font Awesome icon class for a given reaction type.
     *
     * @param string $reaction
     * @return string
     */
    public static function getReactionIcon(string $reaction): string
    {
        return match ($reaction) {
            'like' => 'thumbs-up',
            'love' => 'heart',
            'care' => 'paw',
            'wow' => 'surprise',
            default => 'thumbs-up',
        };
    }

    /**
     * Capitalize the first letter of a string.
     *
     * @param string $string
     * @return string
     */
    public static function capitalizeFirstLetter(string $string): string
    {
        return ucfirst($string);
    }
} 