<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class BlogController extends Controller
{
    #[OA\Get(
        path: '/api/blogs',
        summary: 'Get all blog posts',
        tags: ['Blogs'],
        parameters: [
            new OA\Parameter(name: 'lang', in: 'query', description: 'Language code (e.g., en, ms)', required: false, schema: new OA\Schema(type: 'string', default: 'en')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of blog posts', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/BlogListItem'))),
        ],
    )]
    public function index(Request $request): JsonResponse
    {
        $lang = $request->query('lang', 'en');

        $blogs = Blog::with(['translations.language', 'author'])->get();

        $processedBlogs = $blogs->map(function ($blog) use ($lang) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTitle($lang),
                'summary' => substr($blog->getContent($lang), 0, 150).'...',
                'comment_count' => $blog->comment_count,
                'reaction_count' => $blog->reaction_count,
                'author' => $blog->author->email,
                'published_at' => $blog->published_at,
            ];
        });

        return response()->json($processedBlogs);
    }

    #[OA\Post(
        path: '/api/blogs',
        summary: 'Create a new blog post',
        tags: ['Blogs'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content', 'language_id'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 200, example: 'My New Blog'),
                    new OA\Property(property: 'content', type: 'string', example: 'Full content of the blog post...'),
                    new OA\Property(property: 'language_id', type: 'integer', example: 1),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Blog created', content: new OA\JsonContent(ref: '#/components/schemas/BlogStoreResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - insufficient permissions'),
        ],
    )]
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'language_id' => 'required|exists:languages,id',
        ]);

        $blog = Blog::create([
            'author_id' => auth('api')->id(),
            'comment_count' => 0,
            'reaction_count' => 0,
        ]);

        $blog->translations()->create([
            'language_id' => $request->language_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Blog structural draft created successfully!',
            'blog_id' => $blog->id,
        ], 201);
    }

    #[OA\Put(
        path: '/api/blogs/{id}',
        summary: 'Update a blog post',
        tags: ['Blogs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Blog post ID', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 200, example: 'Updated Title'),
                    new OA\Property(property: 'content', type: 'string', example: 'Updated content...'),
                    new OA\Property(property: 'language_id', type: 'integer', example: 1),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Blog updated', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - insufficient permissions'),
            new OA\Response(response: 404, description: 'Blog not found'),
        ],
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:200',
            'content' => 'sometimes|required|string',
            'language_id' => 'sometimes|required|exists:languages,id',
        ]);

        if ($request->has('title') || $request->has('content')) {
            $languageId = $request->language_id ?? optional($blog->translations()->first())->language_id;
            $translation = $blog->translations()->where('language_id', $languageId)->first();

            if ($translation) {
                $translation->update($request->only(['title', 'content']));
            } else {
                $blog->translations()->create([
                    'language_id' => $languageId,
                    'title' => $request->title ?? '',
                    'content' => $request->content ?? '',
                ]);
            }
        }

        return response()->json(['message' => 'Blog updated successfully!']);
    }

    #[OA\Get(
        path: '/api/blogs/{id}',
        summary: 'Get a single blog post by ID',
        tags: ['Blogs'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Blog post ID', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'lang', in: 'query', description: 'Language code (e.g., en, ms)', required: false, schema: new OA\Schema(type: 'string', default: 'en')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Blog post details', content: new OA\JsonContent(ref: '#/components/schemas/BlogDetail')),
            new OA\Response(response: 404, description: 'Blog not found'),
        ],
    )]
    public function show(Request $request, string $id): JsonResponse
    {
        $lang = $request->query('lang', 'en');

        $blog = Blog::with([
            'translations.language',
            'sections.translations.language',
            'comments.user',
        ])->findOrFail($id);

        return response()->json([
            'id' => $blog->id,
            'title' => $blog->getTitle($lang),
            'content' => $blog->getContent($lang),
            'author' => $blog->author->email,
            'published_at' => $blog->published_at,
            'reaction_count' => $blog->reaction_count,
            'sections' => $blog->sections->map(fn ($section) => [
                'id' => $section->id,
                'order' => $section->order,
                'image' => $section->image ? asset('storage/'.$section->image) : null,
                'title' => $section->getTitle($lang),
                'content' => $section->getContent($lang),
            ]),
            'comments' => $blog->comments->map(fn ($comment) => [
                'id' => $comment->id,
                'user' => $comment->user->email,
                'content' => $comment->content,
                'reply_to_id' => $comment->reply_to_id,
                'created_at' => $comment->created_at,
            ]),
        ]);
    }
}
