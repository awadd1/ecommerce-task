<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        
        if (!$request->user()) {
            return $this->unauthorizedResponse('You must be logged in to access this resource');
        }

      
        if (!empty($roles) && !$request->user()->hasAnyRole($roles)) {
            return $this->forbiddenResponse('You do not have permission to access this resource');
        }

        return $next($request);
    }
}
