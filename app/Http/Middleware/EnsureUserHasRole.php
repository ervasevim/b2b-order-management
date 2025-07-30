<?php

namespace App\Http\Middleware;

use App\Http\Trait\HttpResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    use HttpResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $this->unauthorized(
                401,
                'Unauthorized'
            );
        }

        if (!$request->user()->hasRole($role)) {
            return $this->unauthorized(
                403,
                'Forbidden - You do not have access to this resource'
            );
        }

        return $next($request);
    }
}
