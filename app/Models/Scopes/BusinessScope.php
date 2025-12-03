<?php

namespace App\Models\Scopes;

use App\Support\BusinessContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BusinessScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Avoid scoping the User model to prevent auth recursion.
        if ($model instanceof \App\Models\User) {
            return;
        }

        $businessId = BusinessContext::id() ?? Auth::user()?->business_id;

        if ($businessId) {
            $builder->where($model->getTable().'.business_id', $businessId);
        }
    }
}
