<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    protected array $keys = [
        'platform_name',
        'platform_tagline',
        'primary_color',
        'logo_url',
        'favicon_url',
    ];

    public function edit(): View
    {
        $settings = collect($this->keys)
            ->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);

        return view('admin.settings', ['settings' => $settings]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform_name' => ['nullable', 'string', 'max:255'],
            'platform_tagline' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
        ]);

        foreach (['platform_name', 'platform_tagline', 'primary_color'] as $field) {
            Setting::set($field, $data[$field] ?? null);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            Setting::set('logo_url', Storage::url($path));
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('branding', 'public');
            Setting::set('favicon_url', Storage::url($path));
        }

        Cache::forget('app.settings');

        return back()->with('status', 'Platform settings updated.');
    }
}
