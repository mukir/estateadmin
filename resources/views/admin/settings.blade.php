<x-app-layout>
    <div class="bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-500 text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-indigo-100 text-sm">Admin · Platform</p>
                    <h1 class="text-3xl font-bold">Platform settings</h1>
                    <p class="text-indigo-100 text-sm">Update branding and defaults for the whole system.</p>
                </div>
                <a href="{{ route('admin.businesses.index') }}" class="inline-flex items-center gap-2 rounded-md bg-white/15 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25">
                    Back to admin
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @include('business.partials.status')

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-slate-900">Branding</h2>
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700">Platform name</label>
                    <input name="platform_name" value="{{ old('platform_name', $settings['platform_name']) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200" placeholder="Estate Admin">
                    <x-input-error class="mt-1" :messages="$errors->get('platform_name')" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Tagline</label>
                    <input name="platform_tagline" value="{{ old('platform_tagline', $settings['platform_tagline']) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200" placeholder="Property ops, simplified.">
                    <x-input-error class="mt-1" :messages="$errors->get('platform_tagline')" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Primary color (hex)</label>
                    <input name="primary_color" value="{{ old('primary_color', $settings['primary_color']) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200" placeholder="#4f46e5">
                    <x-input-error class="mt-1" :messages="$errors->get('primary_color')" />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="mt-2 block w-full text-sm">
                    @if ($settings['logo_url'])
                        <p class="text-xs text-slate-500">Current: <a class="text-indigo-600 underline" href="{{ $settings['logo_url'] }}" target="_blank">view</a></p>
                    @endif
                    <x-input-error class="mt-1" :messages="$errors->get('logo')" />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Favicon</label>
                    <input type="file" name="favicon" accept="image/*" class="mt-2 block w-full text-sm">
                    @if ($settings['favicon_url'])
                        <p class="text-xs text-slate-500">Current: <a class="text-indigo-600 underline" href="{{ $settings['favicon_url'] }}" target="_blank">view</a></p>
                    @endif
                    <x-input-error class="mt-1" :messages="$errors->get('favicon')" />
                </div>

                <div class="md:col-span-2 flex justify-end gap-2">
                    <x-primary-button class="px-5">Save settings</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
