<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Support\BusinessContext;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'business_slug' => ['required', 'string'],
            'business_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $slug = Str::slug($request->string('business_slug'));
        $business = Business::where('slug', $slug)->first();
        $createdNew = false;

        if (! $business) {
            $business = Business::create([
                'name' => $request->business_name,
                'slug' => $this->uniqueSlug($slug),
                'contact_email' => $request->email,
                'status' => 'trial',
                'plan' => 'basic',
                'trial_ends_at' => now()->addDays(14),
            ]);
            $createdNew = true;

            foreach (['admin', 'manager', 'accountant', 'viewer'] as $roleName) {
                Role::firstOrCreate(['business_id' => $business->id, 'name' => $roleName]);
            }
        }

        $existing = User::where('business_id', $business->id)
            ->where('email', $request->email)
            ->exists();

        if ($existing) {
            return back()->withErrors(['email' => 'An account already exists for this business.']);
        }

        $user = User::create([
            'business_id' => $business->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'is_business_owner' => $createdNew,
        ]);

        if ($createdNew) {
            $adminRole = Role::where('business_id', $business->id)->where('name', 'admin')->first();
            if ($adminRole) {
                $user->assignRole($adminRole);
            }
        } else {
            $viewerRole = Role::firstOrCreate(['business_id' => $business->id, 'name' => 'viewer']);
            $user->assignRole($viewerRole);
        }

        event(new Registered($user));

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
