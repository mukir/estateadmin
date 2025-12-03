<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_slug' => ['required', 'string'],
            'business_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $slug = Str::slug($data['business_slug']);
        $business = Business::where('slug', $slug)->first();

        $createdNew = false;

        if (! $business) {
            $business = Business::create([
                'name' => $data['business_name'],
                'slug' => $this->uniqueSlug($slug),
                'contact_email' => $data['email'],
                'status' => 'trial',
                'plan' => 'basic',
                'trial_ends_at' => now()->addDays(14),
            ]);
            $createdNew = true;

            foreach (['admin', 'manager', 'accountant', 'viewer'] as $roleName) {
                Role::firstOrCreate(['business_id' => $business->id, 'name' => $roleName]);
            }
        }

        $exists = User::where('business_id', $business->id)
            ->where('email', $data['email'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'email' => 'An account already exists for this business.',
            ]);
        }

        $user = User::create([
            'business_id' => $business->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 'active',
            'is_business_owner' => $createdNew,
        ]);

        if ($createdNew) {
            $adminRole = Role::where('business_id', $business->id)->where('name', 'admin')->first();
            $user->assignRole($adminRole ?? 'admin');
        } else {
            $viewerRole = Role::firstOrCreate(['business_id' => $business->id, 'name' => 'viewer']);
            $user->assignRole($viewerRole);
        }

        Auth::login($user);
        BusinessContext::set($business);

        return redirect()->route('business.dashboard', ['business' => $business->slug]);
    }

    protected function uniqueSlug(string $slug): string
    {
        $base = $slug ?: Str::random(6);
        $counter = 1;
        $unique = $base;

        while (Business::where('slug', $unique)->exists()) {
            $unique = "{$base}-{$counter}";
            $counter++;
        }

        return $unique;
    }
}
