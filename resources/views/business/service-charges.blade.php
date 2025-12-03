@php
    $businessSlug = request()->route('business');
    $chargesCount = $charges->count();
    $estatesCount = $estates->count();
    $totalCharges = $charges->sum('amount');
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Service charges
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">Shared costs</h1>
                        <p class="text-emerald-50 max-w-2xl">Define recurring estate charges and keep billing aligned with each community.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $chargesCount }} charges
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $estatesCount }} estates covered
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            KES {{ number_format($totalCharges, 0) }} total
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Billing playbook</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                Keep in sync
                            </span>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Charges</p>
                                <p class="text-xl font-semibold text-white">{{ $chargesCount }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Avg amount</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($charges->avg('amount') ?? 0, 0) }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Estates</p>
                                <p class="text-xl font-semibold text-white">{{ $estatesCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Invoice with charges
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.estates', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Manage estates
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('business.partials.status')

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Add service charge</h3>
                        <p class="text-sm text-slate-500">Attach to an estate to keep billing aligned.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        Recurring
                    </span>
                </div>
                <form method="POST" action="{{ route('app.service-charges.store', ['business' => $businessSlug]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Estate</label>
                        <select name="estate_id" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" required>
                            <option value="">Select estate</option>
                            @foreach ($estates as $estate)
                                <option value="{{ $estate->id }}">{{ $estate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Name</label>
                        <input name="name" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Security, garbage collection" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Amount</label>
                        <input name="amount" type="number" step="0.01" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Billing cycle</label>
                        <input name="billing_cycle" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Monthly" required>
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button class="px-5">Add charge</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Charges</h3>
                        <p class="text-sm text-slate-500">Keep cycles consistent for easier billing.</p>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Estate</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Name</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Amount</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Cycle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($charges as $charge)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $charge->estate->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $charge->name }}</td>
                                    <td class="py-3 px-4 text-slate-900">KES {{ number_format($charge->amount, 2) }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $charge->billing_cycle }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
