<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StructuredLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_is_logged_to_structured_channel()
    {
        // We use a real log check or just verify the middleware is active
        Log::shouldReceive('channel')
            ->with('structured')
            ->andReturnSelf()
            ->shouldReceive('info')
            ->with('Request Processed', \Mockery::on(function ($context) {
                return isset($context['request']) && isset($context['response']);
            }))
            ->once();

        $this->get('/up');
    }
}
