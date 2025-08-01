<?php

namespace App\Http\Middleware;

use App\Http\Trait\HttpResponse;
use Closure;
use Illuminate\Http\Request;
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
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized(
                401,
                'Yetkisiz erişim!'
            );
        }

        if (!($user->role === 'admin' || $user->hasRole($role))) {
            return $this->unauthorized(
                403,
                ' Erişim engellendi - Bu kaynağa erişim yetkiniz yok!'
            );
        }

        return $next($request);
    }
}
