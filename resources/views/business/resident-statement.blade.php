@php
    $businessSlug = request()->route('business');
    $estateName = $resident->estate->name ?? '-';
    $houseCode = $resident->house->house_code ?? '-';
    $balanceLabel = $runningBalance > 0 ? 'Balance due' : 'In credit';
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Statement
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">{{ $resident->full_name }}</h1>
                        <p class="text-emerald-50">Estate: {{ $estateName }} • House: {{ $houseCode }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ ucfirst($resident->resident_type ?? 'Resident') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold capitalize">
                            {{ $resident->status ?? 'Active' }}
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg space-y-3">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>{{ $balanceLabel }}</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                {{ $runningBalance > 0 ? 'Collect' : 'Settled' }}
                            </span>
                        </div>
                        <p class="text-3xl font-semibold text-white">KES {{ number_format($runningBalance, 2) }}</p>
                        <div class="flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                View invoices
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.payments', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Record payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Statement</h3>
                        <p class="text-sm text-slate-500">Chronological ledger of invoices and payments.</p>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Date</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Type</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Reference</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Amount</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($statement as $row)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $row['date'] }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ ucfirst($row['type']) }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $row['reference'] ?? '-' }}</td>
                                    <td class="py-3 px-4 {{ $row['type'] === 'payment' ? 'text-emerald-700 font-semibold' : 'text-slate-900' }}">
                                        {{ number_format($row['amount'], 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-900">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
