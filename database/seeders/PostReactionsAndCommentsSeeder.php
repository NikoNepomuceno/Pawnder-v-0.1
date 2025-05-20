<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\PostComment;
use App\Models\PostReaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostReactionsAndCommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all posts and users
        $posts = Post::all();
        $users = User::all();
        
        if ($posts->isEmpty() || $users->isEmpty()) {
            $this->command->info('No posts or users found. Please run necessary seeders first.');
            return;
        }
        
        // For each post, add random reactions and comments
        foreach ($posts as $post) {
            // Add 1-5 reactions per post
            $reactionCount = rand(1, 5);
            $reactors = $users->random($reactionCount);
            
            foreach ($reactors as $user) {
                $reactionTypes = ['like', 'love', 'care', 'wow'];
                
                PostReaction::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'reaction_type' => $reactionTypes[array_rand($reactionTypes)]
                ]);
            }
            
            // Add 0-3 comments per post
            $commentCount = rand(0, 3);
            $commenters = $users->random($commentCount);
            
            foreach ($commenters as $user) {
                $comment = PostComment::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'content' => $this->getRandomComment()
                ]);
                
                // 30% chance of having a reply
                if (rand(1, 10) <= 3) {
                    // Get a random user other than the original commenter
                    $otherUsers = $users->except($user->id);
                    if ($otherUsers->isNotEmpty()) {
                        $replier = $otherUsers->random();
                        
                        PostComment::create([
                            'user_id' => $replier->id,
                            'post_id' => $post->id,
                            'parent_id' => $comment->id,
                            'content' => $this->getRandomReply()
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('Sample reactions and comments added successfully!');
    }
    
    /**
     * Get a random comment text.
     */
    private function getRandomComment(): string
    {
        $comments = [
            'This is such an adorable pet!',
            'I hope they find their way home soon.',
            'I think I saw a similar pet around my neighborhood.',
            'Have you tried checking local shelters?',
            'Thanks for posting this! Sharing with my friends.',
            'How long has the pet been missing?',
            'Such a beautiful breed!',
            'I love dogs/cats like this!',
            'Any updates on your search?',
            'Did you check with local vets in the area?'
        ];
        
        return $comments[array_rand($comments)];
    }
    
    /**
     * Get a random reply text.
     */
    private function getRandomReply(): string
    {
        $replies = [
            'Thank you for your comment!',
            'I\'ll try that, thanks for the suggestion.',
            'Yes, we\'ve already done that.',
            'Great idea, will do!',
            'I appreciate your help!',
            'Can you provide more details?',
            'That\'s exactly what we need!',
            'I\'ll keep you updated.',
            'Thank you for your support!',
            'We\'re doing everything we can.'
        ];
        
        return $replies[array_rand($replies)];
    }
}
