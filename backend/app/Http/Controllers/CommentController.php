<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Add a comment or nested reply to a specific blog post (Authenticated).
     */
    public function store(Request $request, string $blogId): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'reply_to_id' => 'nullable|exists:blog_comments,id', // Validates self-referencing links
        ]);

        $blog = Blog::findOrFail($blogId);

        // Save comment
        $comment = BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => auth('api')->id(),
            'content' => $request->content,
            'reply_to_id' => $request->reply_to_id,
        ]);

        // Increment cached comment count atomicity helper
        $blog->increment('comment_count');

        return response()->json([
            'message' => 'Comment appended successfully.',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => auth('api')->user()->email,
                'created_at' => $comment->created_at
            ]
        ], 201);
    }
}