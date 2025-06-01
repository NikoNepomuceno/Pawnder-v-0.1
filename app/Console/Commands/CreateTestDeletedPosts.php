<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateTestDeletedPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:create-test-deleted {--count=3 : Number of test posts to create} {--days-old=35 : How many days old the deletion should be}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test posts with old deletion dates for testing the cleanup functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $daysOld = (int) $this->option('days-old');

        // Get the first user to assign posts to
        $user = User::first();
        if (!$user) {
            $this->error('No users found in the database. Please create a user first.');
            return 1;
        }

        $this->info("Creating {$count} test posts deleted {$daysOld} days ago for user: {$user->username}");

        $deletionDate = Carbon::now()->subDays($daysOld);

        for ($i = 1; $i <= $count; $i++) {
            $post = Post::create([
                'user_id' => $user->id,
                'title' => "Test Post {$i} (Old Deleted)",
                'description' => "This is a test post created for testing the 30-day cleanup functionality. Created on " . now()->toDateString(),
                'status' => 'not_found',
                'breed' => 'Test Breed',
                'location' => 'Test Location',
                'mobile_number' => '1234567890',
                'email' => $user->email,
                'photo_urls' => [],
                'created_at' => $deletionDate->copy()->subDays(1), // Created 1 day before deletion
            ]);

            // Soft delete the post with the old date
            $post->delete();

            // Manually update the deleted_at timestamp to simulate old deletion
            $post->update(['deleted_at' => $deletionDate]);

            $this->line("Created test post ID: {$post->id} - '{$post->title}' (deleted {$deletionDate->diffForHumans()})");
        }

        $this->info("Successfully created {$count} test deleted posts.");
        $this->warn("These posts are now {$daysOld} days old and should be cleaned up by the posts:cleanup-old-deleted command.");

        return 0;
    }
}
