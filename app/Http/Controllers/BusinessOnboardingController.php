<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Support\BusinessContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BusinessOnboardingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $slug = $this->uniqueSlug(Str::slug($data['business_name']));

        $business = Business::create([
            'name' => $data['business_name'],
            'slug' => $slug,
            'contact_email' => $data['email'],
            'contact_phone' => $data['phone'],
            'status' => 'trial',
            'plan' => 'basic',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $user = User::create([
            'business_id' => $business->id,
            'name' => $data['admin_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_business_owner' => true,
            'status' => 'active',
        ]);

        $adminRole = Role::firstOrCreate(['business_id' => $business->id, 'name' => 'admin']);
        foreach (['manager', 'accountant', 'viewer'] as $roleName) {
            Role::firstOrCreate(['business_id' => $business->id, 'name' => $roleName]);
        }
        $user->roles()->syncWithoutDetaching([
            $adminRole->id => ['business_id' => $business->id],
        ]);

        BusinessContext::set($business);

        return response()->json([
            'message' => 'Business created. Welcome to your trial.',
            'business' => $business,
            'owner' => $user,
            'dashboard_url' => url("/b/{$business->slug}/dashboard"),
        ], 201);
    }

    protected function uniqueSlug(string $slug): string
    {
        $base = $slug;
        $counter = 1;

        while (Business::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
