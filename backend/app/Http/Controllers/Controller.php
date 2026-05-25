<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Vue Laravel Template API',
    description: 'API documentation for the Vue Laravel Template project',
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'API server',
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter JWT Bearer token',
)]
// --- Response Schemas ---
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
        new OA\Property(property: 'first_name', type: 'string', nullable: true, example: 'John'),
        new OA\Property(property: 'last_name', type: 'string', nullable: true, example: 'Doe'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'is_superuser', type: 'boolean', example: false),
        new OA\Property(property: 'is_staff', type: 'boolean', example: false),
    ],
)]
#[OA\Schema(
    schema: 'TokenResponse',
    properties: [
        new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1Qi...'),
        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
        new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
    ],
)]
#[OA\Schema(
    schema: 'BlogListItem',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'My Blog Post'),
        new OA\Property(property: 'summary', type: 'string', example: 'This is a summary...'),
        new OA\Property(property: 'comment_count', type: 'integer', example: 5),
        new OA\Property(property: 'reaction_count', type: 'integer', example: 12),
        new OA\Property(property: 'author', type: 'string', format: 'email', example: 'author@example.com'),
        new OA\Property(property: 'published_at', type: 'string', nullable: true, example: '2024-01-01T00:00:00Z'),
    ],
)]
#[OA\Schema(
    schema: 'BlogDetail',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'My Blog Post'),
        new OA\Property(property: 'content', type: 'string', example: 'Full content of the blog post...'),
        new OA\Property(property: 'author', type: 'string', format: 'email', example: 'author@example.com'),
        new OA\Property(property: 'published_at', type: 'string', nullable: true, example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'reaction_count', type: 'integer', example: 12),
        new OA\Property(property: 'sections', type: 'array', items: new OA\Items(ref: '#/components/schemas/BlogSection')),
        new OA\Property(property: 'comments', type: 'array', items: new OA\Items(ref: '#/components/schemas/BlogComment')),
    ],
)]
#[OA\Schema(
    schema: 'BlogSection',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'order', type: 'integer', example: 0),
        new OA\Property(property: 'image', type: 'string', nullable: true, example: 'http://localhost:8000/storage/images/photo.jpg'),
        new OA\Property(property: 'title', type: 'string', example: 'Section Title'),
        new OA\Property(property: 'content', type: 'string', example: 'Section content...'),
    ],
)]
#[OA\Schema(
    schema: 'BlogComment',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user', type: 'string', format: 'email', example: 'commenter@example.com'),
        new OA\Property(property: 'content', type: 'string', example: 'Great post!'),
        new OA\Property(property: 'reply_to_id', type: 'integer', nullable: true, example: null),
        new OA\Property(property: 'created_at', type: 'string', example: '2024-01-01T00:00:00Z'),
    ],
)]
#[OA\Schema(
    schema: 'Language',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'English'),
        new OA\Property(property: 'code', type: 'string', example: 'en'),
        new OA\Property(property: 'default', type: 'boolean', example: true),
    ],
)]
#[OA\Schema(
    schema: 'ErrorResponse',
    properties: [
        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized / Invalid Credentials'),
    ],
)]
#[OA\Schema(
    schema: 'MessageResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Successfully logged out'),
    ],
)]
#[OA\Schema(
    schema: 'CommentResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Comment appended successfully.'),
        new OA\Property(property: 'comment', ref: '#/components/schemas/BlogComment'),
    ],
)]
#[OA\Schema(
    schema: 'ReactionResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Reaction registered'),
        new OA\Property(property: 'status', type: 'string', example: 'attached'),
    ],
)]
#[OA\Schema(
    schema: 'BlogStoreResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Blog structural draft created successfully!'),
        new OA\Property(property: 'blog_id', type: 'integer', example: 1),
    ],
)]
abstract class Controller
{
    //
}
