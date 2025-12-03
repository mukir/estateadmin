@php
    $businessSlug = request()->route('business');
    $arrearsTotal = $arrears->sum('balance');
    $arrearsCount = $arrears->count();
    $collectionsTotal = $payments->sum('amount');
    $plannedUnits = $occupancy->sum(fn ($row) => $row->planned_units ?? $row->planned_units_count ?? 0);
    $occupiedUnits = $occupancy->sum(fn ($row) => $row->occupied_units ?? $row->occupied_units_count ?? 0);
    $vacantUnits = max($plannedUnits - $occupiedUnits, 0);
    $fillRate = $plannedUnits > 0 ? round(($occupiedUnits / max($plannedUnits, 1)) * 100) : 0;
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Reports
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">Performance reports</h1>
                        <p class="text-emerald-50 max-w-2xl">Arrears, collections, and occupancy in one view. Export-ready for finance and operations.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            KES {{ number_format($arrearsTotal, 0) }} arrears
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            KES {{ number_format($collectionsTotal, 0) }} collected
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $fillRate }}% occupancy
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Portfolio health</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                {{ $arrearsTotal > 0 ? 'Action needed' : 'All clear' }}
                            </span>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Arrears</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($arrearsTotal, 0) }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Collections</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($collectionsTotal, 0) }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Vacant</p>
                                <p class="text-xl font-semibold text-white">{{ $vacantUnits }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Review invoices
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.payments', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Reconcile payments
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Arrears</h3>
                        <p class="text-sm text-slate-500">Outstanding balances by invoice.</p>
                    </div>
                    <a
                        href="{{ route('app.reports.export', ['business' => request()->route('business'), 'type' => 'arrears']) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-xs font-semibold text-indigo-700 border border-indigo-100 shadow-sm hover:-translate-y-0.5 transition"
                    >
                        Export CSV
                    </a>
                    <span class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 border border-rose-100">
                        {{ $arrearsCount }} open
                    </span>
                </div>
                    <div class="overflow-hidden rounded-xl border border-slate-100">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-slate-50">
                                <tr class="divide-x divide-slate-100">
                                    <th class="py-3 px-4 font-semibold text-slate-700">Period</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">House</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Resident</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($arrears as $invoice)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4 font-medium text-slate-900">{{ $invoice->billing_period }}</td>
                                        <td class="py-3 px-4 text-slate-700">{{ $invoice->house->house_code ?? '-' }}</td>
                                        <td class="py-3 px-4 text-slate-700">{{ $invoice->resident->full_name ?? '-' }}</td>
                                        <td class="py-3 px-4 text-rose-600 font-semibold">KES {{ number_format($invoice->balance, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Aging buckets</h3>
                            <p class="text-sm text-slate-500">Balances by days overdue.</p>
                        </div>
                        <a
                            href="{{ route('app.reports.export', ['business' => request()->route('business'), 'type' => 'invoice-status']) }}"
                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-xs font-semibold text-indigo-700 border border-indigo-100 shadow-sm hover:-translate-y-0.5 transition"
                        >
                            Invoice status CSV
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-xs text-slate-500">0-30 days</p>
                            <p class="text-lg font-semibold text-slate-900">KES {{ number_format($aging['0-30'], 2) }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-xs text-slate-500">31-60 days</p>
                            <p class="text-lg font-semibold text-slate-900">KES {{ number_format($aging['31-60'], 2) }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-xs text-slate-500">61-90 days</p>
                            <p class="text-lg font-semibold text-slate-900">KES {{ number_format($aging['61-90'], 2) }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-xs text-slate-500">90+ days</p>
                            <p class="text-lg font-semibold text-slate-900">KES {{ number_format($aging['90+'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Log follow-up</h3>
                            <p class="text-sm text-slate-500">Call/SMS/email attempts, notes, and next action.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('app.followups.store', ['business' => request()->route('business')]) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Resident</label>
                            <select name="resident_id" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                                @foreach ($residents as $resident)
                                    <option value="{{ $resident->id }}">{{ $resident->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Invoice (optional)</label>
                            <select name="invoice_id" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                                <option value="">None</option>
                                @foreach ($openInvoices as $invoice)
                                    <option value="{{ $invoice->id }}">{{ $invoice->billing_period }} - {{ $invoice->resident->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Channel</label>
                                <select name="channel" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                                    <option value="call">Call</option>
                                    <option value="sms">SMS</option>
                                    <option value="email">Email</option>
                                    <option value="visit">Visit</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Status tag</label>
                                <select name="status_tag" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                                    <option value="">None</option>
                                    <option value="promise_to_pay">Promise to pay</option>
                                    <option value="dispute">Dispute</option>
                                    <option value="reminder">Reminder</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Next action date</label>
                            <input type="date" name="next_action_date" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Notes</label>
                            <textarea name="notes" rows="3" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="What was agreed?"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <x-primary-button class="px-5">Log follow-up</x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-2 rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Recent follow-ups</h3>
                            <p class="text-sm text-slate-500">Last 20 actions.</p>
                        </div>
                        <a
                            href="{{ route('app.reports.export', ['business' => request()->route('business'), 'type' => 'payments']) }}"
                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-xs font-semibold text-indigo-700 border border-indigo-100 shadow-sm hover:-translate-y-0.5 transition"
                        >
                            Payments CSV
                        </a>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-slate-100">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-slate-50">
                                <tr class="divide-x divide-slate-100">
                                    <th class="py-3 px-4 font-semibold text-slate-700">When</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Resident</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Invoice</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Channel</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Status</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Next action</th>
                                    <th class="py-3 px-4 font-semibold text-slate-700">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($followUps as $follow)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4 font-medium text-slate-900">{{ $follow->created_at->toDateString() }}</td>
                                        <td class="py-3 px-4 text-slate-700">{{ $follow->resident->full_name ?? '-' }}</td>
                                        <td class="py-3 px-4 text-slate-700">
                                            {{ $follow->invoice?->billing_period ?? '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-700 capitalize">{{ $follow->channel }}</td>
                                        <td class="py-3 px-4 text-slate-700">{{ $follow->status_tag ?? '-' }}</td>
                                        <td class="py-3 px-4 text-slate-700">{{ $follow->next_action_date ? $follow->next_action_date->toDateString() : '-' }}</td>
                                        <td class="py-3 px-4 text-slate-700">{{ $follow->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Occupancy</h3>
                        <p class="text-sm text-slate-500">Planned vs occupied by estate.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        {{ $fillRate }}% overall
                    </span>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Estate</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Planned</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Occupied</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Vacant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($occupancy as $row)
                                @php
                                    $planned = $row->planned_units ?? $row->planned_units_count;
                                    $occupied = $row->occupied_units ?? $row->occupied_units_count;
                                    $vacant = max($planned - $occupied, 0);
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $row->name }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $planned }}</td>
                                    <td class="py-3 px-4 text-emerald-700 font-semibold">{{ $occupied }}</td>
                                    <td class="py-3 px-4 text-amber-700 font-semibold">{{ $vacant }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
