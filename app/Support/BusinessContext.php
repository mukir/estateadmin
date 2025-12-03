<?php

namespace App\Support;

use App\Models\Business;

class BusinessContext
{
    protected static ?Business $business = null;

    public static function set(Business $business): void
    {
        static::$business = $business;
    }

    public static function forget(): void
    {
        static::$business = null;
    }

    public static function get(): ?Business
    {
        return static::$business;
    }

    public static function id(): ?int
    {
        return static::$business?->id;
    }

    public static function resolveFromUser($user): void
    {
        if (! $user || ! $user->business_id) {
            return;
        }

        static::set($user->business);
    }
}
