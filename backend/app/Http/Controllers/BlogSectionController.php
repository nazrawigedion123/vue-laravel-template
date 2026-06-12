<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Support\Facades\Gate;
use App\Models\BlogSection;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;
class BlogSectionController extends Controller{

    private function normalizeSectionTranslations(Request $request, Language $defaultLanguage): ?JsonResponse
    {
        if (!$request->has('translations')) {
            return null;
        }

        $translations = $request->input('translations');

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

        if (!is_array($translations)) {
            return null;
        }

        $translations = array_map(function ($item) {
            if (is_string($item)) {
                $decoded = json_decode($item, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $item;
            }

            return $item;
        }, $translations);

        $translations = array_values(array_filter($translations, function ($translation) use ($defaultLanguage) {
            if (!is_array($translation)) {
                return false;
            }

            if ((int) ($translation['language_id'] ?? 0) === $defaultLanguage->id) {
                return true;
            }

            return trim((string) ($translation['title'] ?? '')) !== ''
                || trim((string) ($translation['content'] ?? '')) !== '';
        }));

        $request->merge(['translations' => $translations]);

        return null;
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

        $languages = Language::all();
        $defaultLanguage = Language::where('default', true)->firstOrFail();
        Gate::authorize('own-blog', $blog);

        if ($response = $this->normalizeSectionTranslations($request, $defaultLanguage)) {
            return $response;
        }

        $request->validate([
            'order' => 'required|integer',
            // 'image' => 'nullable|image|max:2048',
            'translations' => 'required|array|min:1',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'nullable|string|max:200',
            'translations.*.content' => 'nullable|string',
             'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:medias,id'
        ]);

        $defaultTranslation = collect($request->input('translations', []))
            ->firstWhere('language_id', $defaultLanguage->id);

        if (
            !$defaultTranslation
            || trim((string) ($defaultTranslation['title'] ?? '')) === ''
            || trim((string) ($defaultTranslation['content'] ?? '')) === ''
        ) {
            return response()->json([
                'message' => "The title and content for the default language ({$defaultLanguage->name}) are required.",
                'errors' => ['translations' => ["Missing default language title/content (ID: {$defaultLanguage->id})"]]
            ], 422);
        }

        $imagePath = $request->file('image') ? $request->file('image')->store('blog_sections', 'public') : null;

        $section = $blog->sections()->create([
            'order' => $request->order,
            // 'image' => $imagePath,
        ]);

        foreach ($request->translations as $translationData) {
            $section->translations()->create([
                'language_id' => $translationData['language_id'],
                'title' => $translationData['title'] ?? '',
                'content' => $translationData['content'] ?? '',
            ]);
        }
        $section->media()->sync($request->input('media_ids'));

        return response()->json([
            'message' => 'Section added successfully!',
            'section' =>[
                        'id' => $section->id,
                        'order' => $section->order,
                        'medias' => $section->media->map(fn ($media) => [
                                        'id' => $media->id,
                                        'filename' => $media->filename,
                                        'mime_type' => $media->mime_type,
                                        // Automatically determines if it's an external YouTube link or a physical file
                                        'url' => str_starts_with($media->mime_type, 'external/') 
                                            ? $media->url 
                                            : Storage::disk('public')->url($media->url),
                                    ]),
                        'translations' => $languages->map(function($lang) use ($section) 
                                                {
                                                $t = $section->translations->firstWhere('language_id', $lang->id);
                                                return [
                                                        'language_id' => $lang->id,
                                                        'language_code' => $lang->code,
                                                        'title' => $t ? $t->title : null,
                                                        'content' => $t ? $t->content : null,
                                                        ];
                                                } )                   
                         ], 201]);
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
    Gate::authorize('own-blog', $blog);
    $languages = Language::all();
    $defaultLanguage = Language::where('default', true)->firstOrFail();
    $section = $blog->sections()->where('id', $sectionId)->firstOrFail();

    if ($response = $this->normalizeSectionTranslations($request, $defaultLanguage)) {
        return $response;
    }

    // Validate request
    $request->validate([
        'order' => 'sometimes|integer', // 'sometimes' makes it optional for updates
        'translations' => 'sometimes|array|min:1',
        'translations.*.language_id' => 'required_with:translations|exists:languages,id',
        'translations.*.title' => 'nullable|string|max:200',
        'translations.*.content' => 'nullable|string',
        'media_ids' => 'nullable|array',
        'media_ids.*' => 'exists:medias,id'
    ]);

    $defaultTranslation = collect($request->input('translations', []))
        ->firstWhere('language_id', $defaultLanguage->id);

    if (
        $request->has('translations')
        && (
            !$defaultTranslation
            || trim((string) ($defaultTranslation['title'] ?? '')) === ''
            || trim((string) ($defaultTranslation['content'] ?? '')) === ''
        )
    ) {
        return response()->json([
            'message' => "The title and content for the default language ({$defaultLanguage->name}) are required.",
            'errors' => ['translations' => ["Missing default language title/content (ID: {$defaultLanguage->id})"]]
        ], 422);
    }

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
                    'title' => $translationData['title'] ?? '',
                    'content' => $translationData['content'] ?? '',
                ]);
            } else {
                // Create new translation
                $section->translations()->create([
                    'language_id' => $translationData['language_id'],
                    'title' => $translationData['title'] ?? '',
                    'content' => $translationData['content'] ?? '',
                ]);
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
        'section' => [
                        'id' => $section->id,
                        'order' => $section->order,
                        'medias' => $section->media->map(fn ($media) => [
                                        'id' => $media->id,
                                        'filename' => $media->filename,
                                        'mime_type' => $media->mime_type,
                                        // Automatically determines if it's an external YouTube link or a physical file
                                        'url' => str_starts_with($media->mime_type, 'external/') 
                                            ? $media->url 
                                            : Storage::disk('public')->url($media->url),
                                    ]),
                        'translations' => $languages->map(function($lang) use ($section) 
                                                {
                                                $t = $section->translations->firstWhere('language_id', $lang->id);
                                                return [
                                                        'language_id' => $lang->id,
                                                        'language_code' => $lang->code,
                                                        'title' => $t ? $t->title : null,
                                                        'content' => $t ? $t->content : null,
                                                        ];
                                                } )                   
                         ],
    ], 200);
    }



    #[OA\Delete(
    path: '/api/blogs/{blogID}/sections/{sectionID}',
    summary: 'Delete a section from a blog post',
    tags: ['Blogs'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'blogID', in: 'path', description: 'Blog post ID', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'sectionID', in: 'path', description: 'Blog post section ID', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Section deleted successfully', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
        new OA\Response(response: 401, description: 'Unauthenticated'),
        new OA\Response(response: 403, description: 'Forbidden'),
        new OA\Response(response: 404, description: 'Blog or Section not found'),
    ]
    )]
    public function deleteSection(string $blogId, string $sectionId): JsonResponse
    {
        $blog = Blog::findOrFail($blogId);       
        Gate::authorize('own-blog', $blog);
        $section = $blog->sections()->where('id', $sectionId)->firstOrFail();
        
        // Delete associated image if exists
        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }
        
        // Delete translations (will be cascaded if foreign key constraints are set, 
        // but explicit deletion is safer)
        $section->translations()->delete();
        
        // Detach media relationships
        $section->media()->detach();
        
        // Delete the section
        $section->delete();
        
        return response()->json(null, 204);
    }

}
