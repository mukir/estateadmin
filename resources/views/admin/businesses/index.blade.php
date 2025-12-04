@php
    $statuses = [
        'trial' => 'Trial',
        'active' => 'Active',
        'suspended' => 'Suspended',
        'cancelled' => 'Cancelled',
    ];
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold">Businesses</h1>
                    <p class="text-indigo-100">Super-admin view. Manage plans, status, and contacts.</p>
                    <div class="flex gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $businesses->total() }} total
                        </span>
                    </div>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        class="rounded-xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder:text-indigo-100 focus:border-white focus:ring-white"
                        placeholder="Search name, slug, email"
                    >
                    <x-primary-button class="bg-white text-indigo-700 hover:bg-indigo-50">Search</x-primary-button>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @include('business.partials.status')

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Business</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Trial ends</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Created</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($businesses as $business)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $business->name }}</div>
                                    <div class="text-xs text-slate-500">Slug: {{ $business->slug }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div>{{ $business->contact_email ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $business->contact_phone ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ ucfirst($business->plan ?? 'basic') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold border
                                        @if ($business->status === 'active') border-emerald-200 bg-emerald-50 text-emerald-700
                                        @elseif ($business->status === 'trial') border-amber-200 bg-amber-50 text-amber-800
                                        @elseif ($business->status === 'suspended') border-rose-200 bg-rose-50 text-rose-700
                                        @else border-slate-200 bg-slate-50 text-slate-700 @endif">
                                        {{ ucfirst($business->status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ $business->trial_ends_at ? $business->trial_ends_at->format('M j, Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ $business->created_at?->format('M j, Y') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.businesses.edit', $business) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">No businesses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3">
                {{ $businesses->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
