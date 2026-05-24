<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password), // Django's set_password replacement
        ]);

        // Optional: Attach default empty roles profile automatically
        $user->role()->create();

        $token = auth('api')->login($user);
        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // This automatically checks email, decrypts and hashes password to verify matching
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized / Invalid Credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        // Returns currently authenticated user with their specific permission payload
        return response()->json(auth('api')->user()->load('role'));
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function googleCallback(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'code_verifier' => 'required',
        ]);

        // 1. Exchange code for access token
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

        // 2. Get user info from Google
        $userResponse = Http::withToken($accessToken)->get(env('GOOGLE_USERINFO_URL'));

        if ($userResponse->failed()) {
            return response()->json(['error' => 'Failed to fetch user info'], 401);
        }

        $googleUser = $userResponse->json();
        $email = $googleUser['email'];

        // 3. Find or create user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'email' => $email,
                'first_name' => $googleUser['given_name'] ?? null,
                'last_name' => $googleUser['family_name'] ?? null,
                'password' => Hash::make(str()->random(24)),
                'is_active' => true,
            ]);
            $user->role()->create();
        }

        // 4. Issue JWT
        $token = auth('api')->login($user);
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 // Default expiration window
        ]);
    }
}