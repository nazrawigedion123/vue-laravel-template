<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Language;
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

        $blogs = Blog::with([
            'translations.language',
            'author',
            'sections.translations.language',
            'comments.user'
        ])->get();

        $processedBlogs = $blogs->map(function ($blog) use ($lang) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTitle($lang),
                'summary' => $blog->getSummary($lang),
                'content' => $blog->getContent($lang),
                'comment_count' => $blog->comment_count,
                'reaction_count' => $blog->reaction_count,
                'author' => $blog->author->email,
                'published_at' => $blog->published_at,
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
                'translations' => $blog->translations->map(fn($t) => [
                    'language_id' => $t->language_id,
                    'language_code' => $t->language->code,
                    'title' => $t->title,
                    'summary' => $t->summary,
                    'content' => $t->content,
                ]),
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
                required: ['translations'],
                properties: [
                    new OA\Property(
                        property: 'translations',
                        type: 'array',
                        items: new OA\Items(
                            required: ['language_id', 'title', 'content'],
                            properties: [
                                new OA\Property(property: 'language_id', type: 'integer', example: 1),
                                new OA\Property(property: 'title', type: 'string', maxLength: 200, example: 'My New Blog'),
                                new OA\Property(property: 'summary', type: 'string', example: 'Short summary of the blog post...'),
                                new OA\Property(property: 'content', type: 'string', example: 'Full content of the blog post...'),
                            ]
                        )
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Blog created', content: new OA\JsonContent(ref: '#/components/schemas/BlogStoreResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - insufficient permissions'),
            new OA\Response(response: 422, description: 'Validation Error'),
        ],
    )]
    public function store(Request $request): JsonResponse
    {
        $defaultLanguage = Language::where('default', true)->firstOrFail();

        $request->validate([
            'translations' => 'required|array|min:1',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:200',
            'translations.*.summary' => 'nullable|string',
            'translations.*.content' => 'required|string',
        ]);

        $translations = collect($request->translations);
        
        if (!$translations->contains('language_id', $defaultLanguage->id)) {
            return response()->json([
                'message' => "The translation for the default language ({$defaultLanguage->name}) is required.",
                'errors' => ['translations' => ["Missing default language translation (ID: {$defaultLanguage->id})"]]
            ], 422);
        }

        $blog = Blog::create([
            'author_id' => auth('api')->id(),
            'comment_count' => 0,
            'reaction_count' => 0,
        ]);

        foreach ($request->translations as $translationData) {
            $blog->translations()->create($translationData);
        }

        return response()->json([
            'message' => 'Blog created successfully with all provided translations!',
            'blog_id' => $blog->id,
        ], 201);
    }

    #[OA\Post(
        path: '/api/blogs/{id}/sections',
        summary: 'Add a section to a blog post',
        tags: ['Blogs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Blog post ID', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['order', 'translations'],
                    properties: [
                        new OA\Property(property: 'order', type: 'integer', example: 1),
                        new OA\Property(property: 'image', type: 'string', format: 'binary'),
                        new OA\Property(
                            property: 'translations',
                            type: 'array',
                            items: new OA\Items(
                                required: ['language_id', 'title', 'content'],
                                properties: [
                                    new OA\Property(property: 'language_id', type: 'integer', example: 1),
                                    new OA\Property(property: 'title', type: 'string', maxLength: 200, example: 'Section Title'),
                                    new OA\Property(property: 'content', type: 'string', example: 'Section content...'),
                                ]
                            )
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Section added', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Blog not found'),
            new OA\Response(response: 422, description: 'Validation Error'),
        ],
    )]
    public function addSection(Request $request, string $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);

        if ($request->has('translations')) {
            $translations = $request->translations;

            if (is_string($translations)) {
                $translations = json_decode($translations, true);
            }

            if (is_array($translations)) {
                $translations = array_map(function ($item) {
                    return is_string($item) ? json_decode($item, true) : $item;
                }, $translations);
                $request->merge(['translations' => $translations]);
            }
        }

        $request->validate([
            'order' => 'required|integer',
            'image' => 'nullable|image|max:2048',
            'translations' => 'required|array|min:1',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:200',
            'translations.*.content' => 'required|string',
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('blog_sections', 'public') : null;

        $section = $blog->sections()->create([
            'order' => $request->order,
            'image' => $imagePath,
        ]);

        foreach ($request->translations as $translationData) {
            $section->translations()->create($translationData);
        }

        return response()->json([
            'message' => 'Section added successfully!',
            'section_id' => $section->id,
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
                    new OA\Property(
                        property: 'translations',
                        type: 'array',
                        items: new OA\Items(
                            required: ['language_id', 'title', 'content'],
                            properties: [
                                new OA\Property(property: 'language_id', type: 'integer', example: 1),
                                new OA\Property(property: 'title', type: 'string', maxLength: 200, example: 'Updated Title'),
                                new OA\Property(property: 'summary', type: 'string', example: 'Updated summary...'),
                                new OA\Property(property: 'content', type: 'string', example: 'Updated content...'),
                            ]
                        )
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Blog updated', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - insufficient permissions'),
            new OA\Response(response: 404, description: 'Blog not found'),
            new OA\Response(response: 422, description: 'Validation Error'),
        ],
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'translations' => 'required|array|min:1',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:200',
            'translations.*.summary' => 'nullable|string',
            'translations.*.content' => 'required|string',
        ]);

        foreach ($request->translations as $translationData) {
            $blog->translations()->updateOrCreate(
                ['language_id' => $translationData['language_id']],
                [
                    'title' => $translationData['title'],
                    'summary' => $translationData['summary'] ?? null,
                    'content' => $translationData['content'],
                ]
            );
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
            new OA\Response(response: 200, description: 'Blog post details', content: new OA\JsonContent(ref: '#/components/schemas/BlogListItem')),
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
            'summary' => $blog->getSummary($lang),
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
            'translations' => $blog->translations->map(fn($t) => [
                'language_id' => $t->language_id,
                'language_code' => $t->language->code,
                'title' => $t->title,
                'summary' => $t->summary,
                'content' => $t->content,
            ]),
        ]);
    }
}
