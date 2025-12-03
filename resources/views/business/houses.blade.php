<x-app-layout>
    @php
        $businessSlug = request()->route('business');
        $houseCount = $houses->count();
        $occupiedCount = $houses->where('is_occupied', true)->count();
        $vacantCount = max($houseCount - $occupiedCount, 0);
        $fillRate = $houseCount > 0 ? round(($occupiedCount / max($houseCount, 1)) * 100) : 0;
        $averageCharge = round($houses->avg('default_service_charge') ?? 0, 2);
    @endphp

    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Houses / Units
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">Unit inventory</h1>
                        <p class="text-emerald-50 max-w-2xl">List every unit, keep service charges handy, and monitor vacancies in seconds.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $houseCount }} units
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $fillRate }}% occupied
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            Avg SC: KES {{ number_format($averageCharge, 2) }}
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Occupancy</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                {{ $vacantCount > 0 ? 'Fill vacancies' : 'Fully let' }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-end justify-between">
                            <p class="text-3xl font-semibold text-white">{{ $occupiedCount }}/{{ $houseCount }}</p>
                            <p class="text-xs text-emerald-100">Occupied</p>
                        </div>
                        <div class="mt-4 h-3 w-full rounded-full bg-white/20">
                            <div
                                class="h-3 rounded-full bg-gradient-to-r from-white to-emerald-100"
                                style="width: {{ $fillRate }}%; max-width: 100%;"
                            ></div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Vacant</p>
                                <p class="text-xl font-semibold text-white">{{ $vacantCount }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Avg SC</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($averageCharge, 2) }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Blocks</p>
                                <p class="text-xl font-semibold text-white">{{ $houses->whereNotNull('block')->unique('block')->count() }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.residents', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Assign residents
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.service-charges', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Service charges
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12" x-data="{ filters: { query: '' } }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('business.partials.status')

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Import houses (CSV)</h3>
                        <p class="text-sm text-slate-500">Headers: estate_code, house_code, block, house_type, default_service_charge, is_occupied (yes/no).</p>
                    </div>
                    <a
                        href="{{ route('app.import.template', ['business' => $businessSlug, 'type' => 'houses']) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-sm font-semibold text-indigo-700 border border-indigo-100 shadow-sm hover:-translate-y-0.5 transition"
                    >
                        Download template
                    </a>
                </div>
                <form method="POST" action="{{ route('app.import.houses', ['business' => $businessSlug]) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="file" name="file" accept=".csv,text/csv" class="flex-1 rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                    <x-primary-button class="px-5">Upload</x-primary-button>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Add house</h3>
                        <p class="text-sm text-slate-500">Block, type, and charges help drive faster billing.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        New unit
                    </span>
                </div>
                <form method="POST" action="{{ route('app.houses.store', ['business' => $businessSlug]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <label class="block text-sm font-medium text-slate-700">House code</label>
                        <input name="house_code" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="A-101" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">House type</label>
                        <input name="house_type" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="2 bed, studio">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Block</label>
                        <input name="block" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Block A">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Default service charge</label>
                        <input name="default_service_charge" type="number" step="0.01" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="0.00">
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button class="px-5">Add house</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Houses</h3>
                        <p class="text-sm text-slate-500">Search by code, type, estate, or block.</p>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <input
                            type="search"
                            x-model.debounce.200ms="filters.query"
                            class="w-full rounded-xl border-slate-200 pr-10 focus:border-emerald-400 focus:ring-emerald-200"
                            placeholder="Search units..."
                        >
                        <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-xs">Ctrl+K</span>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Estate</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Code</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Type</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Block</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Default SC</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($houses as $house)
                                @php
                                    $searchText = strtolower(($house->estate->name ?? '').' '.($house->house_code ?? '').' '.($house->house_type ?? '').' '.($house->block ?? ''));
                                @endphp
                                <tr
                                    class="hover:bg-slate-50/70 transition"
                                    x-show="!filters.query || @js($searchText).includes(filters.query.toLowerCase())"
                                >
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $house->estate->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-800">{{ $house->house_code }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $house->house_type }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $house->block }}</td>
                                    <td class="py-3 px-4 text-slate-900">KES {{ number_format($house->default_service_charge, 2) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold border {{ $house->is_occupied ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                            {{ $house->is_occupied ? 'Occupied' : 'Vacant' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
