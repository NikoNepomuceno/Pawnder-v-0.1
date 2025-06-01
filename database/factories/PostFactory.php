<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['found', 'not_found']),
            'breed' => fake()->randomElement(['Golden Retriever', 'Labrador', 'German Shepherd', 'Bulldog', 'Poodle', 'Mixed Breed']),
            'location' => fake()->city() . ', ' . fake()->state(),
            'mobile_number' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'photo_urls' => [],
            'shared_post_id' => null,
            'was_shared' => false,
            'is_flagged' => false,
            'flag_reason' => null,
            'is_taken_down' => false,
        ];
    }
}
