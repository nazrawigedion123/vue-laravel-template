<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class LanguageController extends Controller
{
    #[OA\Get(
        path: '/api/languages',
        summary: 'Get all supported languages',
        tags: ['Languages'],
        responses: [
            new OA\Response(response: 200, description: 'List of languages', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Language'))),
        ],
    )]
    public function index(): JsonResponse
    {
        return response()->json(
            Language::query()
                ->orderByDesc('default')
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'default'])
        );
    }
}
