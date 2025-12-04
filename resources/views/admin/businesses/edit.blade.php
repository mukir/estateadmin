@php
    $statusOptions = ['trial' => 'Trial', 'active' => 'Active', 'suspended' => 'Suspended', 'cancelled' => 'Cancelled'];
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-500 text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-indigo-100 text-sm">Admin · Business</p>
                    <h1 class="text-3xl font-bold">{{ $business->name }}</h1>
                    <p class="text-indigo-100 text-sm">Slug: {{ $business->slug }}</p>
                </div>
                <a href="{{ route('admin.businesses.index') }}" class="inline-flex items-center gap-2 rounded-md bg-white/15 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25">
                    Back to list
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @include('business.partials.status')

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-slate-900">Business details</h2>
            <form method="POST" action="{{ route('admin.businesses.update', $business) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-slate-700">Name</label>
                    <input name="name" value="{{ old('name', $business->name) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200" required>
                    <x-input-error class="mt-1" :messages="$errors->get('name')" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Contact email</label>
                    <input name="contact_email" value="{{ old('contact_email', $business->contact_email) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200" type="email">
                    <x-input-error class="mt-1" :messages="$errors->get('contact_email')" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Contact phone</label>
                    <input name="contact_phone" value="{{ old('contact_phone', $business->contact_phone) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200">
                    <x-input-error class="mt-1" :messages="$errors->get('contact_phone')" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Plan</label>
                    <input name="plan" value="{{ old('plan', $business->plan) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200" placeholder="basic">
                    <x-input-error class="mt-1" :messages="$errors->get('plan')" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Status</label>
                    <select name="status" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $business->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-1" :messages="$errors->get('status')" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Trial ends at</label>
                    <input name="trial_ends_at" type="date" value="{{ old('trial_ends_at', optional($business->trial_ends_at)->toDateString()) }}" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-200">
                    <x-input-error class="mt-1" :messages="$errors->get('trial_ends_at')" />
                </div>

                <div class="md:col-span-2 flex justify-end gap-2">
                    <x-primary-button class="px-5">Save changes</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
