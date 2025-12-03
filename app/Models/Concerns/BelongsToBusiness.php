<?php

namespace App\Models\Concerns;

use App\Models\Business;
use App\Models\Scopes\BusinessScope;
use App\Support\BusinessContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToBusiness
{
    public static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope(new BusinessScope);

        static::creating(function ($model): void {
            if ($model instanceof \App\Models\User) {
                return;
            }

            if ($model->business_id) {
                return;
            }

            $model->business_id = BusinessContext::id() ?? Auth::user()?->business_id;
        });
    }

    public function scopeForBusiness(Builder $query, int $businessId): Builder
    {
        return $query->withoutGlobalScope(BusinessScope::class)->where('business_id', $businessId);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
