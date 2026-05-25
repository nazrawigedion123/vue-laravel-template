<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReactionController extends Controller
{
    #[OA\Post(
        path: '/api/blogs/{id}/react',
        summary: 'React to a blog post',
        tags: ['Reactions'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Blog post ID', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['reaction_type'],
                properties: [
                    new OA\Property(property: 'reaction_type', type: 'string', enum: ['like', 'love', 'haha', 'wow', 'sad', 'angry'], example: 'like'),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Reaction added', content: new OA\JsonContent(ref: '#/components/schemas/ReactionResponse')),
            new OA\Response(response: 200, description: 'Reaction removed or swapped', content: new OA\JsonContent(ref: '#/components/schemas/ReactionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Blog not found'),
        ],
    )]
    public function toggle(Request $request, string $blogId): JsonResponse
    {
        $request->validate([
            'reaction_type' => 'required|string|in:like,love,haha,wow,sad,angry',
        ]);

        $blog = Blog::findOrFail($blogId);
        $userId = auth('api')->id();

        $existingReaction = BlogReaction::where('user_id', $userId)
            ->where('blog_id', $blog->id)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->reaction_type === $request->reaction_type) {
                $existingReaction->delete();
                $blog->decrement('reaction_count');

                return response()->json(['message' => 'Reaction removed', 'status' => 'detached']);
            }

            $existingReaction->update(['reaction_type' => $request->reaction_type]);

            return response()->json(['message' => 'Reaction changed', 'status' => 'swapped']);
        }

        BlogReaction::create([
            'user_id' => $userId,
            'blog_id' => $blog->id,
            'reaction_type' => $request->reaction_type,
        ]);

        $blog->increment('reaction_count');

        return response()->json(['message' => 'Reaction registered', 'status' => 'attached'], 201);
    }
}
