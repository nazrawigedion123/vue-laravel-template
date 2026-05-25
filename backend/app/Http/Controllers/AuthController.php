<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/auth/register',
        summary: 'Register a new user',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', minLength: 3, example: 'secret123'),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'User registered successfully', content: new OA\JsonContent(ref: '#/components/schemas/TokenResponse')),
            new OA\Response(response: 422, description: 'Validation error'),
        ],
    )]
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->role()->create();

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Log in with email and password',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'secret123'),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful', content: new OA\JsonContent(ref: '#/components/schemas/TokenResponse')),
            new OA\Response(response: 401, description: 'Invalid credentials', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized / Invalid Credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    #[OA\Get(
        path: '/api/auth/me',
        summary: 'Get current authenticated user',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Authenticated user data', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ],
    )]
    public function me()
    {
        return response()->json(auth('api')->user()->load('role'));
    }

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Log out the current user',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successfully logged out', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
        ],
    )]
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    #[OA\Post(
        path: '/api/auth/google/callback',
        summary: 'Google OAuth callback',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['code', 'code_verifier'],
                properties: [
                    new OA\Property(property: 'code', type: 'string', example: '4/0AeaYSHB...'),
                    new OA\Property(property: 'code_verifier', type: 'string', example: 'dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk'),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Google login successful', content: new OA\JsonContent(ref: '#/components/schemas/TokenResponse')),
            new OA\Response(response: 401, description: 'Failed to exchange code or fetch user info'),
        ],
    )]
    public function googleCallback(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'code_verifier' => 'required',
        ]);

        $response = Http::post(env('GOOGLE_TOKEN_URL'), [
            'client_id' => env('SOCIAL_AUTH_GOOGLE_CLIENT_ID'),
            'client_secret' => env('SOCIAL_AUTH_GOOGLE_SECRET'),
            'code' => $request->code,
            'code_verifier' => $request->code_verifier,
            'redirect_uri' => env('GOOGLE_REDIRECT_URL'),
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to exchange code', 'details' => $response->json()], 401);
        }

        $accessToken = $response->json()['access_token'];

        $userResponse = Http::withToken($accessToken)->get(env('GOOGLE_USERINFO_URL'));

        if ($userResponse->failed()) {
            return response()->json(['error' => 'Failed to fetch user info'], 401);
        }

        $googleUser = $userResponse->json();
        $email = $googleUser['email'];

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'email' => $email,
                'first_name' => $googleUser['given_name'] ?? null,
                'last_name' => $googleUser['family_name'] ?? null,
                'password' => Hash::make(str()->random(24)),
                'is_active' => true,
            ]);
            $user->role()->create();
        }

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
