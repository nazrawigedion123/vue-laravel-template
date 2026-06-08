<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller
{
    #[OA\Post(
        path: '/api/media/upload',
        summary: 'Upload a new file or link external media',
        description: 'Accepts either a physical file upload (multipart/form-data) OR an external media URL.',
        tags: ['Media'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: [], // Handled dynamically via required_without in Laravel
                    properties: [
                        new OA\Property(
                            property: 'file',
                            description: 'The physical media file (jpg, jpeg, png, gif, mp4, mov, avi). Required if external_url is missing.',
                            type: 'string',
                            format: 'binary' // This triggers the file upload selector in Swagger UI
                        ),
                        new OA\Property(
                            property: 'external_url',
                            description: 'A URL to an external media source (e.g., a YouTube link). Required if file is missing.',
                            type: 'string',
                            format: 'uri',
                            example: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
                        ),
                        new OA\Property(
                            property: 'filename',
                            description: 'An optional custom display name for the media file.',
                            type: 'string',
                            maxLength: 255,
                            example: 'Summer Vacation Hero Image'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: 'Media created successfully', 
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Media created successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'filename', type: 'string', example: 'Summer Vacation Hero Image'),
                                new OA\Property(property: 'url', type: 'string', example: 'media/random_filename.jpg'),
                                new OA\Property(property: 'mime_type', type: 'string', example: 'image/jpeg'),
                                new OA\Property(property: 'file_size', type: 'integer', nullable: true, example: 1048576),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation Error - Missing both file and external_url, or invalid file type'),
        ],
    )]
    public function store(Request $request): JsonResponse
    {
        // 1. Validate the incoming request
        $request->validate([
            'file' => 'required_without:external_url|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480', // Max 20MB
            'external_url' => 'required_without:file|url',
            'filename' => 'nullable|string|max:255'
        ]);

        $mediaData = [
            'user_id' => Auth::id() ?? 1, // Fallback to 1 if testing without auth
        ];

        // Scenario A: User uploaded a physical file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Store file in the 'media' directory on the public disk (or S3)
            $path = $file->store('media', 'public');

            $mediaData['filename'] = $request->input('filename') ?? $file->getClientOriginalName();
            $mediaData['url'] = $path;
            $mediaData['mime_type'] = $file->getMimeType();
            $mediaData['file_size'] = $file->getSize();
        } 
        // Scenario B: User provided an external link (like YouTube)
        else {
            $url = $request->input('external_url');
            $mediaData['url'] = $url;
            $mediaData['filename'] = $request->input('filename') ?? 'External Link';
            $mediaData['file_size'] = null;

            // Determine if it's YouTube or a generic external asset
            if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
                $mediaData['mime_type'] = 'external/youtube';
            } else {
                $mediaData['mime_type'] = 'external/link';
            }
        }

        $media = Media::create($mediaData);

        return response()->json([
            'message' => 'Media created successfully',
            'data' => $media
        ], 201);
    }

    
    
    #[OA\Put(
        path: '/api/media/{id}',
        summary: 'Update media metadata',
        description: 'Updates the display filename of an existing media record. The physical file remains unchanged.',
        tags: ['Media'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'The ID of the media record to update',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['filename'],
                properties: [
                    new OA\Property(
                        property: 'filename',
                        description: 'The new custom display name for the media file.',
                        type: 'string',
                        maxLength: 255,
                        example: 'Updated Vacation Photo Name'
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Media updated successfully', 
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Media updated successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'filename', type: 'string', example: 'Updated Vacation Photo Name'),
                                new OA\Property(property: 'url', type: 'string', example: 'media/random_filename.jpg'),
                                new OA\Property(property: 'mime_type', type: 'string', example: 'image/jpeg'),
                                new OA\Property(property: 'file_size', type: 'integer', nullable: true, example: 1048576),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Media record not found'),
            new OA\Response(response: 422, description: 'Validation Error - Missing or invalid filename'),
        ],
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $media = Media::findOrFail($id);
        Gate::authorize('edit-media', $media);

        $request->validate([
            'filename' => 'required|string|max:255',
        ]);

        // We only update the display name, not the immutable actual file path
        $media->update([
            'filename' => $request->input('filename')
        ]);

        return response()->json([
            'message' => 'Media updated successfully',
            'data' => $media
        ], 200);
    }

    
    


     #[OA\Delete(
        path: '/api/media/{id}',
        summary: 'Delete a media by ID',
        tags: ['Media'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'media ID', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'media deleted'),
            new OA\Response(response: 404, description: 'Blog not found'),
        ],
    )]
    public function destroy(int $id): JsonResponse
    {
        $media = Media::findOrFail($id);

        Gate::authorize('delete-media', $media);

        // Crucial Check: If it's a locally stored file, delete it from the physical storage disk
        if (!str_starts_with($media->mime_type, 'external/')) {
            if (Storage::disk('public')->exists($media->url)) {
                Storage::disk('public')->delete($media->url);
            }
        }

        // Delete the database entry
        $media->delete();

        return response()->json([
            'message' => 'Media and its associated files deleted successfully'
        ], 200);
    }
}