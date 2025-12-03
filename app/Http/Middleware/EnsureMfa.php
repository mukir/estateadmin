<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMfa
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $requires = $user->requiresMfa();
        if (! $requires) {
            return $next($request);
        }

        if (! $user->mfa_enabled) {
            return redirect()->route('mfa.setup')->with('status', 'Please enable MFA.');
        }

        if (! session()->get('mfa_passed')) {
            return redirect()->route('mfa.challenge');
        }

        return $next($request);
    }
}
