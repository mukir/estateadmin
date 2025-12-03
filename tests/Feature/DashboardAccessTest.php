<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $business = Business::create([
            'name' => 'Debug Biz',
            'slug' => 'debug-biz',
            'status' => 'active',
            'plan' => 'basic',
        ]);

        $user = User::create([
            'business_id' => $business->id,
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password123'),
            'is_business_owner' => true,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get("/b/{$business->slug}/dashboard");

        $response->assertStatus(200);
    }
}
