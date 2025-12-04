<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBusinessController extends Controller
{
    public function index(Request $request): View
    {
        $query = Business::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        $businesses = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.businesses.index', compact('businesses'));
    }

    public function edit(Business $business): View
    {
        return view('admin.businesses.edit', compact('business'));
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:trial,active,suspended,cancelled'],
            'plan' => ['required', 'string', 'max:50'],
            'trial_ends_at' => ['nullable', 'date'],
        ]);

        $business->update($data);

        return redirect()
            ->route('admin.businesses.edit', $business)
            ->with('status', 'Business updated.');
    }
}
