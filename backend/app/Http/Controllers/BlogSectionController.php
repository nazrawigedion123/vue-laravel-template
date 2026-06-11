<?php

namespace App\Http\Controllers;

use App\Models\Blog;

use App\Models\BlogSection;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;
class BlogSectionController extends Controller{




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
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['order', 'translations'],
                    properties: [
                        new OA\Property(property: 'order', type: 'integer', example: 1),
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
                        new OA\Property(
                        property: 'media_ids',
                        description: 'An array of existing media IDs to associate with this blog post.',
                        type: 'array',
                        items: new OA\Items(
                            type: 'integer',
                            example: 1
                        ),
                        example: [1, 2, 3]
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
                $decoded = json_decode($translations, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'message' => 'The translations field contains invalid JSON.',
                        'errors' => ['translations' => ['JSON parse error: ' . json_last_error_msg()]]
                    ], 422);
                }
                $translations = $decoded;
            }

            if (is_array($translations)) {
                $translations = array_map(function ($item) {
                    if (is_string($item)) {
                        $decoded = json_decode($item, true);
                        return json_last_error() === JSON_ERROR_NONE ? $decoded : $item;
                    }
                    return $item;
                }, $translations);
                $request->merge(['translations' => $translations]);
            }
        }

        $request->validate([
            'order' => 'required|integer',
            // 'image' => 'nullable|image|max:2048',
            'translations' => 'required|array|min:1',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:200',
            'translations.*.content' => 'required|string',
             'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:medias,id'
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('blog_sections', 'public') : null;

        $section = $blog->sections()->create([
            'order' => $request->order,
            // 'image' => $imagePath,
        ]);

        foreach ($request->translations as $translationData) {
            $section->translations()->create($translationData);
        }
        $section->media()->sync($request->input('media_ids'));

        return response()->json([
            'message' => 'Section added successfully!',
            'section_id' => $section->id,
        ], 201);
    }



    #[OA\Put(
        path: '/api/blogs/{blogID}/sections/{sectionID}',
        summary: 'Update a section ofa blog post',
        tags: ['Blogs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'blogID', in: 'path', description: 'Blog post ID', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sectionID', in: 'path', description: 'Blog post section ID', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['order', 'translations'],
                    properties: [
                        new OA\Property(property: 'order', type: 'integer', example: 1),
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
                        new OA\Property(
                        property: 'media_ids',
                        description: 'An array of existing media IDs to associate with this blog post.',
                        type: 'array',
                        items: new OA\Items(
                            type: 'integer',
                            example: 1
                        ),
                        example: [1, 2, 3]
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
    public function updateSection(Request $request, string $blogId, string $sectionId): JsonResponse
    {
    $blog = Blog::findOrFail($blogId);
    $section = $blog->sections()->where('id', $sectionId)->firstOrFail();

    // Handle translations if present (same logic as addSection)
    if ($request->has('translations')) {
        $translations = $request->translations;

        if (is_string($translations)) {
            $decoded = json_decode($translations, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'The translations field contains invalid JSON.',
                    'errors' => ['translations' => ['JSON parse error: ' . json_last_error_msg()]]
                ], 422);
            }
            $translations = $decoded;
        }

        if (is_array($translations)) {
            $translations = array_map(function ($item) {
                if (is_string($item)) {
                    $decoded = json_decode($item, true);
                    return json_last_error() === JSON_ERROR_NONE ? $decoded : $item;
                }
                return $item;
            }, $translations);
            $request->merge(['translations' => $translations]);
        }
    }

    // Validate request
    $request->validate([
        'order' => 'sometimes|integer', // 'sometimes' makes it optional for updates
        'translations' => 'sometimes|array|min:1',
        'translations.*.language_id' => 'required_with:translations|exists:languages,id',
        'translations.*.title' => 'required_with:translations|string|max:200',
        'translations.*.content' => 'required_with:translations|string',
        'media_ids' => 'nullable|array',
        'media_ids.*' => 'exists:medias,id'
    ]);

    // Handle image update if provided
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('blog_sections', 'public');
        
        // Optional: Delete old image if exists
        // if ($section->image) {
        //     Storage::disk('public')->delete($section->image);
        // }
    }

    // Update section
    $updateData = [];
    if ($request->has('order')) {
        $updateData['order'] = $request->order;
    }
    if ($imagePath) {
        $updateData['image'] = $imagePath;
    }
    
    if (!empty($updateData)) {
        $section->update($updateData);
    }

    // Update translations
    if ($request->has('translations')) {
        foreach ($request->translations as $translationData) {
            // Check if translation exists for this language
            $translation = $section->translations()
                ->where('language_id', $translationData['language_id'])
                ->first();
            
            if ($translation) {
                // Update existing translation
                $translation->update([
                    'title' => $translationData['title'],
                    'content' => $translationData['content'],
                ]);
            } else {
                // Create new translation
                $section->translations()->create($translationData);
            }
        }
    }

    // Sync media if provided
    if ($request->has('media_ids')) {
        $section->media()->sync($request->input('media_ids'));
    }

    // Load relationships for response
    $section->load(['translations', 'media']);

    return response()->json([
        'message' => 'Section updated successfully!',
        'section' => $section,
    ], 200);
    }

}