<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    /**
     * The rate limiter instance.
     */
    protected $limiter;

    /**
     * Create a new rate limiter middleware.
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->ip();

        if ($this->limiter->tooManyAttempts($key, 60)) {
            return response()->json([
                'message' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
                'retry_after' => $this->limiter->availableIn($key)
            ], 429);
        }

        $this->limiter->hit($key);

        $response = $next($request);

        $response->headers->add([
            'X-RateLimit-Limit' => 60,
            'X-RateLimit-Remaining' => $this->limiter->remaining($key, 60),
        ]);

        return $response;
    }
}
