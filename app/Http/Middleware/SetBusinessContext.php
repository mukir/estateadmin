<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Support\BusinessContext;
use Closure;
use Illuminate\Http\Request;

class SetBusinessContext
{
    public function handle(Request $request, Closure $next)
    {
        $business = $this->resolveBusiness($request);

        if ($business) {
            BusinessContext::set($business);
            $request->attributes->set('business', $business);
        }

        return $next($request);
    }

    protected function resolveBusiness(Request $request): ?Business
    {
        $routeBusiness = $request->route('business') ?? $request->route('business_slug');

        if ($routeBusiness instanceof Business) {
            return $routeBusiness;
        }

        if (is_string($routeBusiness)) {
            return Business::where('slug', $routeBusiness)->first();
        }

        if ($subdomain = $this->extractSubdomain($request)) {
            $business = Business::where('slug', $subdomain)->first();
            if ($business) {
                return $business;
            }
        }

        if ($request->user()?->business_id) {
            return $request->user()->business;
        }

        return null;
    }

    protected function extractSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) < 3) {
            return null;
        }

        return $parts[0];
    }
}
