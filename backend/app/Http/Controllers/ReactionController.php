<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogReaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReactionController extends Controller
{
    /**
     * Toggle or swap user blog metric profiles (Authenticated).
     */
    public function toggle(Request $request, string $blogId): JsonResponse
    {
        $request->validate([
            'reaction_type' => 'required|string|in:like,love,haha,wow,sad,angry',
        ]);

        $blog = Blog::findOrFail($blogId);
        $userId = auth('api')->id();

        // Check for existing single reaction setup mapping Django's unique_together constraint
        $existingReaction = BlogReaction::where('user_id', $userId)
            ->where('blog_id', $blog->id)
            ->first();

        if ($existingReaction) {
            // If clicking same button -> Delete reaction (Unlike)
            if ($existingReaction->reaction_type === $request->reaction_type) {
                $existingReaction->delete();
                $blog->decrement('reaction_count');

                return response()->json(['message' => 'Reaction removed', 'status' => 'detached']);
            }

            // If choosing a different emoji -> Swap data string values
            $existingReaction->update(['reaction_type' => $request->reaction_type]);
            return response()->json(['message' => 'Reaction changed', 'status' => 'swapped']);
        }

        // If fresh click -> Instantiate reaction record mapping configuration
        BlogReaction::create([
            'user_id' => $userId,
            'blog_id' => $blog->id,
            'reaction_type' => $request->reaction_type,
        ]);

        $blog->increment('reaction_count');

        return response()->json(['message' => 'Reaction registered', 'status' => 'attached'], 201);
    }
}