<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestResponseLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $startTime;

        Log::channel('structured')->info('Request Processed', [
            'request' => [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'payload' => $this->filterPayload($request->all()),
            ],
            'response' => [
                'status' => $response->getStatusCode(),
                'duration_ms' => round($duration * 1000, 2),
            ],
        ]);

        return $response;
    }

    private function filterPayload(array $payload): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'access_token'];
        foreach ($sensitiveFields as $field) {
            if (isset($payload[$field])) {
                $payload[$field] = '********';
            }
        }
        return $payload;
    }
}
