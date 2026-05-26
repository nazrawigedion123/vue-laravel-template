<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    #[OA\Post(
        path: '/api/blogs/{id}/comment',
        summary: 'Add a comment to a blog post',
        tags: ['Comments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Blog post ID', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'Great article!'),
                    new OA\Property(property: 'reply_to_id', type: 'integer', nullable: true, example: null, description: 'ID of the parent comment for replies'),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Comment created', content: new OA\JsonContent(ref: '#/components/schemas/CommentResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Blog not found'),
        ],
    )]
    public function store(Request $request, string $blogId): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'reply_to_id' => 'nullable|exists:blog_comments,id',
        ]);

        $blog = Blog::findOrFail($blogId);

        $comment = BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => auth('api')->id(),
            'content' => $request->content,
            'reply_to_id' => $request->reply_to_id,
        ]);

        $blog->increment('comment_count');

        return response()->json([
            'message' => 'Comment appended successfully.',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => auth('api')->user()->email,
                'created_at' => $comment->created_at,
            ],
        ], 201);
    }

    #[OA\Delete(
        path: '/api/comments/{id}',
        summary: 'Delete a comment',
        tags: ['Comments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Comment ID', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Comment deleted', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Comment not found'),
        ],
    )]
    public function destroy(string $id): JsonResponse
    {
        $comment = BlogComment::findOrFail($id);

        // Check if user is owner or admin (assuming admin has certain role or just owner for now)
        if ($comment->user_id !== auth('api')->id()) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->blog->decrement('comment_count');
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }
}
