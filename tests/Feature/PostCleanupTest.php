<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PostCleanupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that posts older than 30 days are permanently deleted.
     */
    public function test_posts_older_than_30_days_are_deleted(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create posts with different deletion dates
        $oldPost1 = Post::factory()->create(['user_id' => $user->id]);
        $oldPost2 = Post::factory()->create(['user_id' => $user->id]);
        $recentPost = Post::factory()->create(['user_id' => $user->id]);

        // Soft delete all posts
        $oldPost1->delete();
        $oldPost2->delete();
        $recentPost->delete();

        // Manually set deletion dates
        $oldPost1->update(['deleted_at' => Carbon::now()->subDays(35)]);
        $oldPost2->update(['deleted_at' => Carbon::now()->subDays(40)]);
        $recentPost->update(['deleted_at' => Carbon::now()->subDays(20)]);

        // Verify initial state
        $this->assertEquals(3, Post::onlyTrashed()->count());

        // Run cleanup command
        Artisan::call('posts:cleanup-old-deleted');

        // Verify that only old posts were permanently deleted
        $this->assertEquals(1, Post::onlyTrashed()->count());
        $this->assertTrue(Post::onlyTrashed()->where('id', $recentPost->id)->exists());
        $this->assertFalse(Post::withTrashed()->where('id', $oldPost1->id)->exists());
        $this->assertFalse(Post::withTrashed()->where('id', $oldPost2->id)->exists());
    }

    /**
     * Test that posts newer than 30 days are NOT deleted.
     */
    public function test_posts_newer_than_30_days_are_not_deleted(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create posts deleted within the last 30 days
        $post1 = Post::factory()->create(['user_id' => $user->id]);
        $post2 = Post::factory()->create(['user_id' => $user->id]);
        $post3 = Post::factory()->create(['user_id' => $user->id]);

        // Soft delete all posts
        $post1->delete();
        $post2->delete();
        $post3->delete();

        // Set deletion dates within 30 days
        $post1->update(['deleted_at' => Carbon::now()->subDays(5)]);
        $post2->update(['deleted_at' => Carbon::now()->subDays(15)]);
        $post3->update(['deleted_at' => Carbon::now()->subDays(29)]);

        // Verify initial state
        $this->assertEquals(3, Post::onlyTrashed()->count());

        // Run cleanup command
        Artisan::call('posts:cleanup-old-deleted');

        // Verify that no posts were deleted
        $this->assertEquals(3, Post::onlyTrashed()->count());
        $this->assertTrue(Post::onlyTrashed()->where('id', $post1->id)->exists());
        $this->assertTrue(Post::onlyTrashed()->where('id', $post2->id)->exists());
        $this->assertTrue(Post::onlyTrashed()->where('id', $post3->id)->exists());
    }

    /**
     * Test mixed scenario with both old and new posts.
     */
    public function test_mixed_old_and_new_posts(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create multiple posts
        $veryOldPost = Post::factory()->create(['user_id' => $user->id]);
        $oldPost = Post::factory()->create(['user_id' => $user->id]);
        $borderlinePost = Post::factory()->create(['user_id' => $user->id]);
        $recentPost = Post::factory()->create(['user_id' => $user->id]);

        // Soft delete all posts
        $veryOldPost->delete();
        $oldPost->delete();
        $borderlinePost->delete();
        $recentPost->delete();

        // Set different deletion dates
        $veryOldPost->update(['deleted_at' => Carbon::now()->subDays(60)]);
        $oldPost->update(['deleted_at' => Carbon::now()->subDays(31)]);
        $borderlinePost->update(['deleted_at' => Carbon::now()->subDays(30)->subHour()]); // Just over 30 days
        $recentPost->update(['deleted_at' => Carbon::now()->subDays(29)]);

        // Verify initial state
        $this->assertEquals(4, Post::onlyTrashed()->count());

        // Run cleanup command
        Artisan::call('posts:cleanup-old-deleted');

        // Verify results: only posts older than 30 days should be deleted
        $this->assertEquals(1, Post::onlyTrashed()->count());
        $this->assertTrue(Post::onlyTrashed()->where('id', $recentPost->id)->exists());
        $this->assertFalse(Post::withTrashed()->where('id', $veryOldPost->id)->exists());
        $this->assertFalse(Post::withTrashed()->where('id', $oldPost->id)->exists());
        $this->assertFalse(Post::withTrashed()->where('id', $borderlinePost->id)->exists());
    }
}
