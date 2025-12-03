<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_slug' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $business = null;

        if ($data['business_slug']) {
            $business = Business::where('slug', $data['business_slug'])->first();

            if (! $business) {
                throw ValidationException::withMessages([
                    'business_slug' => 'Business not found.',
                ]);
            }
        }

        $query = User::query()->where('email', $data['email']);

        if ($business) {
            $query->where('business_id', $business->id);
        } else {
            $query->whereNull('business_id');
        }

        $user = $query->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        Auth::login($user, true);

        if ($user->business) {
            BusinessContext::set($user->business);
            return redirect()->route('business.dashboard', ['business' => $user->business->slug]);
        }

        return redirect()->intended('/');
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        BusinessContext::forget();

        return redirect()->route('landing');
    }
}
