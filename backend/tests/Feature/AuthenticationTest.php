<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * Test that unauthenticated API requests return 401 JSON instead of redirecting.
     */
    public function test_unauthenticated_api_request_returns_401_json(): void
    {
        $response = $this->delete('/api/blogs/1', [], [
            'Accept' => '*/*',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test that authenticated API requests work (basic check).
     * This might need a user to be created, but for now we just want to ensure 
     * the redirect issue is fixed.
     */
}
