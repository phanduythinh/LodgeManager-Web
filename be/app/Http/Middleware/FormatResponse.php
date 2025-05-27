<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FormatResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            if (!isset($data['status'])) {
                $data = [
                    'status' => $response->getStatusCode() < 400 ? 'success' : 'error',
                    'data' => $data
                ];

                $response->setData($data);
            }
        }

        return $response;
    }
}
