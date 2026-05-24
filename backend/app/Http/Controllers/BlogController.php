<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs (Public).
     */
    public function index(Request $request): JsonResponse
    {
        $lang = $request->query('lang', 'en');

        // Eager load translations to prevent N+1 queries
        $blogs = Blog::with(['translations.language', 'author'])->get();

        $processedBlogs = $blogs->map(function ($blog) use ($lang) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTitle($lang),
                'summary' => substr($blog->getContent($lang), 0, 150) . '...',
                'comment_count' => $blog->comment_count,
                'reaction_count' => $blog->reaction_count,
                'author' => $blog->author->email,
                'published_at' => $blog->published_at,
            ];
        });

        return response()->json($processedBlogs);
    }

    /**
     * Store a newly created blog in storage (Authenticated + Permission Required).
     */
    public function store(Request $request): JsonResponse
    {
        // Validation rules matching your payload requirements
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'language_id' => 'required|exists:languages,id',
        ]);

        // Create base blog entity
        $blog = Blog::create([
            'author_id' => auth('api')->id(),
            'comment_count' => 0,
            'reaction_count' => 0,
        ]);

        // Attach initial translation entry
        $blog->translations()->create([
            'language_id' => $request->language_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Blog structural draft created successfully!',
            'blog_id' => $blog->id
        ], 201);
    }

    /**
     * Display the specified blog post details (Public).
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $lang = $request->query('lang', 'en');

        $blog = Blog::with([
            'translations.language',
            'sections.translations.language',
            'comments.user'
        ])->findOrFail($id);

        return response()->json([
            'id' => $blog->id,
            'title' => $blog->getTitle($lang),
            'content' => $blog->getContent($lang),
            'author' => $blog->author->email,
            'published_at' => $blog->published_at,
            'reaction_count' => $blog->reaction_count,
            'sections' => $blog->sections->map(fn($section) => [
                'id' => $section->id,
                'order' => $section->order,
                'image' => $section->image ? asset('storage/' . $section->image) : null,
                'title' => $section->getTitle($lang),
                'content' => $section->getContent($lang),
            ]),
            'comments' => $blog->comments->map(fn($comment) => [
                'id' => $comment->id,
                'user' => $comment->user->email,
                'content' => $comment->content,
                'reply_to_id' => $comment->reply_to_id,
                'created_at' => $comment->created_at,
            ])
        ]);
    }
}