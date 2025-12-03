<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Audit
{
    public static function log(string $event, ?string $description = null, array $meta = []): void
    {
        AuditLog::create([
            'business_id' => BusinessContext::id() ?? Auth::user()?->business_id,
            'user_id' => Auth::id(),
            'event' => $event,
            'description' => $description,
            'meta' => $meta,
            'ip_address' => Request::ip(),
        ]);
    }
}
