<?php

namespace App\Http\Middleware;

use App\Support\BusinessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->is_business_owner) {
            return $next($request);
        }

        $allowed = collect(explode(',', $roles))->map(fn ($r) => trim($r))->filter()->values();
        $hasRole = $user->roles->contains(fn ($role) => $allowed->contains($role->name));

        if (! $hasRole) {
            abort(403);
        }

        return $next($request);
    }
}
