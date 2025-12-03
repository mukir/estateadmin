<x-app-layout>
    @php
        $businessName = $business->name ?? 'Portfolio';
        $businessSlug = $business->slug ?? null;
        $occupancyRate = $house_count > 0 ? round(($occupied_units / $house_count) * 100) : 0;
        $netFlow = $collections_total - $arrears_total;
        $maxFlow = max($collections_total, $arrears_total, 1);
        $collectionsPercent = $maxFlow ? round(($collections_total / $maxFlow) * 100) : 0;
        $arrearsPercent = $maxFlow ? round(($arrears_total / $maxFlow) * 100) : 0;

        $setupSteps = [
            ['label' => 'Add estates', 'done' => $estate_count > 0, 'link' => $businessSlug ? route('app.estates', ['business' => $businessSlug]) : '#'],
            ['label' => 'Add units', 'done' => $house_count > 0, 'link' => $businessSlug ? route('app.houses', ['business' => $businessSlug]) : '#'],
            ['label' => 'Add residents', 'done' => ($resident_count ?? 0) > 0, 'link' => $businessSlug ? route('app.residents', ['business' => $businessSlug]) : '#'],
            ['label' => 'Add service charges', 'done' => ($service_charge_count ?? 0) > 0, 'link' => $businessSlug ? route('app.service-charges', ['business' => $businessSlug]) : '#'],
            ['label' => 'Run first billing', 'done' => ($invoice_count ?? 0) > 0, 'link' => $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#'],
        ];
    @endphp

    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        <span>Home</span>
                        <span class="opacity-80">/ Dashboard</span>
                    </div>
                    <div class="space-y-2">
                        <p class="text-lg text-emerald-100">MTD view ({{ now()->startOfMonth()->format('M d') }} - {{ now()->format('M d') }})</p>
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">{{ $businessName }} overview</h1>
                        <p class="text-emerald-50 max-w-2xl">Stay ahead of occupancy, inflows, and open balances. Use the quick actions to nudge collections or add new tenants.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold shadow-sm">
                            Occupancy {{ $occupancyRate }}%
                            <span class="h-2 w-2 rounded-full {{ $occupancyRate >= 90 ? 'bg-emerald-100' : 'bg-amber-200' }}"></span>
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold shadow-sm">
                            {{ $house_count }} units
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold shadow-sm">
                            {{ $estate_count }} estates
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Portfolio pulse</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                {{ $netFlow >= 0 ? 'Healthy' : 'Action needed' }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-end justify-between">
                            <p class="text-3xl font-semibold text-white">KES {{ number_format(abs($netFlow), 2) }}</p>
                            <p class="text-xs text-emerald-100">{{ $netFlow >= 0 ? 'Net inflow' : 'Net outflow' }}</p>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.payments', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Record payment
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Issue invoice
                            </a>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a
                            href="{{ $businessSlug ? route('app.estates', ['business' => $businessSlug]) : '#' }}"
                            class="rounded-xl bg-white/10 border border-white/20 px-3 py-3 text-sm font-semibold text-white hover:bg-white/15 transition"
                        >
                            Add estate
                        </a>
                        <a
                            href="{{ $businessSlug ? route('app.residents', ['business' => $businessSlug]) : '#' }}"
                            class="rounded-xl bg-white/10 border border-white/20 px-3 py-3 text-sm font-semibold text-white hover:bg-white/15 transition"
                        >
                            Add tenant
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-5">
                    <p class="text-sm text-slate-500">Collections (MTD)</p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-semibold text-emerald-600">KES {{ number_format($collections_total, 2) }}</p>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">Inflow</span>
                    </div>
                </div>
                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-5">
                    <p class="text-sm text-slate-500">Arrears outstanding</p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-semibold text-rose-600">KES {{ number_format($arrears_total, 2) }}</p>
                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 border border-rose-100">Follow up</span>
                    </div>
                </div>
                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-5">
                    <p class="text-sm text-slate-500">Net flow</p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-semibold {{ $netFlow >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">KES {{ number_format($netFlow, 2) }}</p>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold border {{ $netFlow >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                            {{ $netFlow >= 0 ? 'Positive' : 'Negative' }}
                        </span>
                    </div>
                </div>
                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-5">
                    <p class="text-sm text-slate-500">Occupied units</p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-semibold text-slate-900">{{ $occupied_units }}</p>
                        <p class="text-sm text-slate-500">of {{ $house_count }}</p>
                    </div>
                </div>
                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-5">
                    <p class="text-sm text-slate-500">Vacant units</p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <p class="text-3xl font-semibold text-amber-600">{{ $vacant_units }}</p>
                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-100 rounded-full px-3 py-1">Fill units</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Occupancy & capacity</h3>
                            <p class="text-sm text-slate-500">Monitor filled units and keep a pulse on vacancies.</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                            {{ $occupancyRate }}% occupied
                        </span>
                    </div>
                    <div class="mt-4 h-3 w-full rounded-full bg-slate-100">
                        <div
                            class="h-3 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"
                            style="width: {{ $occupancyRate }}%; max-width: 100%;"
                        ></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                            <p class="text-xs text-slate-500">Total units</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $house_count }}</p>
                        </div>
                        <div class="rounded-lg bg-emerald-50 px-3 py-2">
                            <p class="text-xs text-emerald-700">Occupied</p>
                            <p class="text-lg font-semibold text-emerald-700">{{ $occupied_units }}</p>
                        </div>
                        <div class="rounded-lg bg-amber-50 px-3 py-2">
                            <p class="text-xs text-amber-700">Vacant</p>
                            <p class="text-lg font-semibold text-amber-700">{{ $vacant_units }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                            <p class="text-xs text-slate-500">Estates</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $estate_count }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Collections vs arrears</h3>
                            <p class="text-sm text-slate-500">Progress toward clearing outstanding balances.</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200 shadow-sm">
                            Live
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm text-slate-600">
                                <span>Collections</span>
                                <span>KES {{ number_format($collections_total, 2) }}</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div
                                    class="h-2 rounded-full bg-emerald-500"
                                    style="width: {{ $collectionsPercent }}%; max-width: 100%;"
                                ></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm text-slate-600">
                                <span>Arrears</span>
                                <span>KES {{ number_format($arrears_total, 2) }}</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div
                                    class="h-2 rounded-full bg-rose-500"
                                    style="width: {{ $arrearsPercent }}%; max-width: 100%;"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Net balance</span>
                            <span class="{{ $netFlow >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">KES {{ number_format($netFlow, 2) }}</span>
                        </div>
                        <p class="text-xs text-slate-500">Keep collections above arrears to stay healthy.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Quick links</h3>
                            <p class="text-sm text-slate-500">Jump to the screens you need most.</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                            {{ $businessSlug ?? 'Workspace' }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        <a
                            href="{{ $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#' }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 hover:border-emerald-200 hover:bg-emerald-50 transition"
                        >
                            <span>Invoices & billing</span>
                            <span class="text-emerald-600">View</span>
                        </a>
                        <a
                            href="{{ $businessSlug ? route('app.payments', ['business' => $businessSlug]) : '#' }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 hover:border-emerald-200 hover:bg-emerald-50 transition"
                        >
                            <span>Payments</span>
                            <span class="text-emerald-600">Track</span>
                        </a>
                        <a
                            href="{{ $businessSlug ? route('app.residents', ['business' => $businessSlug]) : '#' }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 hover:border-emerald-200 hover:bg-emerald-50 transition"
                        >
                            <span>Residents & leases</span>
                            <span class="text-emerald-600">Manage</span>
                        </a>
                        <a
                            href="{{ $businessSlug ? route('app.reports', ['business' => $businessSlug]) : '#' }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 hover:border-emerald-200 hover:bg-emerald-50 transition"
                        >
                            <span>Reports</span>
                            <span class="text-emerald-600">Export</span>
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Pipeline</h3>
                            <p class="text-sm text-slate-500">Keep the vacancy queue moving.</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 border border-amber-100">
                            Vacant {{ $vacant_units }}
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span>Occupied units</span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ $occupied_units }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                <span>Vacant units</span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ $vacant_units }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                <span>Total inventory</span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ $house_count }}</span>
                        </div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <p class="text-sm font-semibold text-slate-900">Vacancy playbook</p>
                        <p class="text-xs text-slate-600 mt-1">List units, send reminders, and schedule viewings from the Residents screen.</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.houses', ['business' => $businessSlug]) : '#' }}"
                                class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow hover:-translate-y-0.5 transition"
                            >
                                List units
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.residents', ['business' => $businessSlug]) : '#' }}"
                                class="rounded-lg bg-white px-3 py-2 text-xs font-semibold text-emerald-700 border border-emerald-200 hover:-translate-y-0.5 transition"
                            >
                                Schedule viewing
                            </a>
                        </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Reports</h3>
                        <p class="text-sm text-slate-500">Download core statements in a click.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 border border-indigo-100">
                        Export
                    </span>
                </div>
                <div class="space-y-2">
                    <a
                        href="{{ $businessSlug ? url('/b/'.$businessSlug.'/reports/arrears') : '#' }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 hover:border-indigo-200 hover:bg-indigo-50 transition"
                        >
                            <span>Arrears report</span>
                            <span class="text-indigo-600">Open</span>
                        </a>
                        <a
                            href="{{ $businessSlug ? url('/b/'.$businessSlug.'/reports/collections') : '#' }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 hover:border-indigo-200 hover:bg-indigo-50 transition"
                        >
                            <span>Collections report</span>
                            <span class="text-indigo-600">Open</span>
                        </a>
                        <a
                            href="{{ $businessSlug ? url('/b/'.$businessSlug.'/reports/occupancy') : '#' }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 hover:border-indigo-200 hover:bg-indigo-50 transition"
                        >
                            <span>Occupancy report</span>
                            <span class="text-indigo-600">Open</span>
                        </a>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <p class="text-xs text-slate-600">Tip: share reports with your team or export as CSV for your accountant.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-6 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Setup checklist</h3>
                        <p class="text-sm text-slate-500">Complete these steps to finish onboarding.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @foreach ($setupSteps as $step)
                        <a
                            href="{{ $step['link'] }}"
                            class="flex items-center gap-3 rounded-xl border px-3 py-3 text-sm font-semibold transition {{ $step['done'] ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-800 hover:border-emerald-200' }}"
                        >
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-xs font-bold {{ $step['done'] ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ $step['done'] ? '✓' : '•' }}
                            </span>
                            <span>{{ $step['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
