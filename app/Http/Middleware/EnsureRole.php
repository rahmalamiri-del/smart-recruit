<?php

namespace SmartRecruit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = sr_user()['role'] ?? 'guest';

        if (in_array($role, $roles, true)) {
            return $next($request);
        }

        if ($role === 'guest') {
            return redirect()->route('login')->with('error', 'Connectez-vous pour accéder à cette page.');
        }

        abort(403);
    }
}
