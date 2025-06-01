<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserController extends Controller
{
    public function viewProfile()
    {
        $user = Auth::user();
        $posts = Post::where('user_id', Auth::id())->latest()->get();
        return view('auth.view-profile', compact('user', 'posts'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|string|url',
            'banner_image' => 'nullable|string|url',
        ]);

        if ($request->profile_picture) {
            // Delete old profile picture from Cloudinary if exists
            if ($user->profile_picture) {
                try {
                    $urlParts = parse_url($user->profile_picture);
                    if ($urlParts && isset($urlParts['path'])) {
                        $pathParts = pathinfo($urlParts['path']);
                        if (isset($pathParts['filename'])) {
                            // Extract public ID from the URL path
                            $publicId = $pathParts['filename'];
                            Cloudinary::destroy($publicId);
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but continue
                    Log::error('Failed to delete old profile picture: ' . $e->getMessage());
                }
            }
            $user->profile_picture = $request->profile_picture;
        }

        if ($request->banner_image) {
            // Delete old banner image from Cloudinary if exists
            if ($user->banner_image) {
                try {
                    $urlParts = parse_url($user->banner_image);
                    if ($urlParts && isset($urlParts['path'])) {
                        $pathParts = pathinfo($urlParts['path']);
                        if (isset($pathParts['filename'])) {
                            // Extract public ID from the URL path
                            $publicId = $pathParts['filename'];
                            Cloudinary::destroy($publicId);
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but continue
                    Log::error('Failed to delete old banner image: ' . $e->getMessage());
                }
            }
            $user->banner_image = $request->banner_image;
        }

        $user->username = $request->username;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePost(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $request->validate([
            'content' => 'required|string',
            'title' => 'required|string|max:255',
        ]);

        $post->update($request->all());
        return redirect()->back()->with('success', 'Post updated successfully');
    }

    public function deletePost(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $post->delete();
        return redirect()->back()->with('success', 'Post moved to trash successfully');
    }
}
