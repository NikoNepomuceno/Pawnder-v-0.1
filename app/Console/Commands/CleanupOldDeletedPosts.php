<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupOldDeletedPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:cleanup-old-deleted {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete posts that have been in trash for more than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of old deleted posts...');

        // Get posts deleted more than 30 days ago
        $cutoffDate = Carbon::now()->subDays(30);

        $oldDeletedPosts = Post::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->get();

        if ($oldDeletedPosts->isEmpty()) {
            $this->info('No posts found that are older than 30 days in trash.');
            return 0;
        }

        $count = $oldDeletedPosts->count();

        if ($this->option('dry-run')) {
            $this->warn("DRY RUN: Would permanently delete {$count} posts:");
            foreach ($oldDeletedPosts as $post) {
                $this->line("- Post ID: {$post->id}, Title: {$post->title}, Deleted: {$post->deleted_at->diffForHumans()}");
            }
            return 0;
        }

        // Confirm deletion unless running in non-interactive mode or testing
        if (!app()->environment('testing') && !$this->confirm("Are you sure you want to permanently delete {$count} posts? This action cannot be undone.")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $deletedCount = 0;
        foreach ($oldDeletedPosts as $post) {
            try {
                $this->line("Permanently deleting post ID: {$post->id} ('{$post->title}')");
                $post->forceDelete();
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to delete post ID: {$post->id}. Error: {$e->getMessage()}");
            }
        }

        $this->info("Successfully permanently deleted {$deletedCount} out of {$count} posts.");

        return 0;
    }
}
