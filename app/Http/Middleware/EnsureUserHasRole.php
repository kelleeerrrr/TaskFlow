<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            abort(Response::HTTP_FORBIDDEN, 'Account is not active.');
        }

        if (! $role) {
            return $next($request);
        }

        $allowedRoles = explode('|', $role);

        if (! in_array($user->role, $allowedRoles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'This action is unauthorized.');
        }

        return $next($request);
    }
}
