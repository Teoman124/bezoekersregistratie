<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Accepts a comma separated list of roles, e.g. role:admin,employee
     */
    public function handle(Request $request, Closure $next, ?string $roles = null)
{
    $user = $request->user();

    if (! $user) {
        abort(403);
    }

    if ($roles) {
        $allowed = array_map('trim', explode(',', $roles));

        if (! in_array($user->role, $allowed, true)) {
            abort(403);
        }
    }

    return $next($request);
}
}
