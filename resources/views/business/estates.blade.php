<x-app-layout>
    @php
        $businessSlug = request()->route('business');
        $plannedTotal = $estates->sum('planned_units');
        $occupiedTotal = $estates->sum('occupied_units');
        $vacantTotal = max($plannedTotal - $occupiedTotal, 0);
        $fillRate = $plannedTotal > 0 ? round(($occupiedTotal / max($plannedTotal, 1)) * 100) : 0;
    @endphp

    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Estates
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">Estates & communities</h1>
                        <p class="text-emerald-50 max-w-2xl">Track occupancy across every estate, add new stock, and keep addresses tidy for invoices and residents.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $estates->count() }} estates
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            Fill rate {{ $fillRate }}%
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $plannedTotal }} units planned
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Portfolio mix</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                {{ $vacantTotal > 0 ? 'Fill pipeline' : 'Healthy' }}
                            </span>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Planned</p>
                                <p class="text-xl font-semibold text-white">{{ $plannedTotal }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Occupied</p>
                                <p class="text-xl font-semibold text-white">{{ $occupiedTotal }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Vacant</p>
                                <p class="text-xl font-semibold text-white">{{ $vacantTotal }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.houses', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Add houses
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.residents', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Add residents
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12" x-data="estatesPage()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('business.partials.status')

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Import estates (CSV)</h3>
                        <p class="text-sm text-slate-500">Use template headers: name, code, type, address, planned_units.</p>
                    </div>
                    <a
                        href="{{ route('app.import.template', ['business' => $businessSlug, 'type' => 'estates']) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-sm font-semibold text-indigo-700 border border-indigo-100 shadow-sm hover:-translate-y-0.5 transition"
                    >
                        Download template
                    </a>
                </div>
                <form method="POST" action="{{ route('app.import.estates', ['business' => $businessSlug]) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="file" name="file" accept=".csv,text/csv" class="flex-1 rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                    <x-primary-button class="px-5">Upload</x-primary-button>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Add estate</h3>
                        <p class="text-sm text-slate-500">Keep codes unique so units can be traced quickly.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        New stock
                    </span>
                </div>
                <form method="POST" action="{{ route('app.estates.store', ['business' => $businessSlug]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Name</label>
                        <input name="name" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Code</label>
                        <input name="code" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Unique short code">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Type</label>
                        <input name="type" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Apartments, gated...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Address</label>
                        <input name="address" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Street, city">
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button class="px-5">Add estate</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Estates</h3>
                        <p class="text-sm text-slate-500">Search by name, code, or type.</p>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <input
                            type="search"
                            x-model.debounce.200ms="filters.query"
                            class="w-full rounded-xl border-slate-200 pr-10 focus:border-emerald-400 focus:ring-emerald-200"
                            placeholder="Search estates..."
                        >
                        <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-xs">Ctrl+K</span>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Name</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Code</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Type</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Planned</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Occupied</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Vacant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($estates as $estate)
                                @php
                                    $vacant = max(($estate->planned_units ?? 0) - ($estate->occupied_units ?? 0), 0);
                                @endphp
                                <tr
                                    class="hover:bg-slate-50/70 transition"
                                    x-data="{ search: @js(strtolower($estate->name.' '.($estate->code ?? '').' '.($estate->type ?? ''))) }"
                                    x-show="!filters.query || search.includes(filters.query.toLowerCase())"
                                >
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $estate->name }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $estate->code }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $estate->type }}</td>
                                    <td class="py-3 px-4 text-slate-900">{{ $estate->planned_units }}</td>
                                    <td class="py-3 px-4 text-emerald-700 font-semibold">{{ $estate->occupied_units }}</td>
                                    <td class="py-3 px-4 text-amber-700 font-semibold">{{ $vacant }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('estatesPage', () => ({
                filters: { query: '' },
            }));
        });
    </script>
</x-app-layout>
