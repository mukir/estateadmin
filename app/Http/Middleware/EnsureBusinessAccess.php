<?php

namespace App\Http\Middleware;

use App\Support\BusinessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureBusinessAccess
{
    public function handle(Request $request, Closure $next)
    {
        $business = BusinessContext::get();
        $user = $request->user();

        if ($business && $user && $user->business_id && $business->id !== $user->business_id && ! $user->isSuperAdmin()) {
            throw new AccessDeniedHttpException('You cannot access another business.');
        }

        if ($business && $user && $user->isSuperAdmin()) {
            $user->business_id = $business->id;
        }

        return $next($request);
    }
}
