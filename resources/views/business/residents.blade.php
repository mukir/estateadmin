@php
    $businessSlug = request()->route('business');
    $houseOptions = $houses->map(fn ($house) => [
        'id' => $house->id,
        'label' => $house->house_code,
        'estate_id' => $house->estate_id,
    ]);
    $residentCount = $residents->count();
    $attachedCount = $residents->whereNotNull('house_id')->count();
    $estatesCount = $estates->count();
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Residents
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">People & leases</h1>
                        <p class="text-emerald-50 max-w-2xl">Capture contact details, link residents to homes, and open statements in a click.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $residentCount }} residents
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $attachedCount }} linked to houses
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $estatesCount }} estates
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Engagement</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                {{ $attachedCount > 0 ? 'Active tenancies' : 'Start linking' }}
                            </span>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Residents</p>
                                <p class="text-xl font-semibold text-white">{{ $residentCount }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Linked</p>
                                <p class="text-xl font-semibold text-white">{{ $attachedCount }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Unlinked</p>
                                <p class="text-xl font-semibold text-white">{{ max($residentCount - $attachedCount, 0) }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Invoice resident
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.houses', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                View units
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12" x-data="residentPage({ houses: @js($houseOptions) })">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('business.partials.status')

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Import residents (CSV)</h3>
                        <p class="text-sm text-slate-500">Headers: estate_code, house_code, full_name, email, phone, resident_type, status.</p>
                    </div>
                    <a
                        href="{{ route('app.import.template', ['business' => $businessSlug, 'type' => 'residents']) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-sm font-semibold text-indigo-700 border border-indigo-100 shadow-sm hover:-translate-y-0.5 transition"
                    >
                        Download template
                    </a>
                </div>
                <form method="POST" action="{{ route('app.import.residents', ['business' => $businessSlug]) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="file" name="file" accept=".csv,text/csv" class="flex-1 rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                    <x-primary-button class="px-5">Upload</x-primary-button>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Add resident</h3>
                        <p class="text-sm text-slate-500">Estate selection filters the houses list automatically.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        New profile
                    </span>
                </div>
                <form method="POST" action="{{ route('app.residents.store', ['business' => $businessSlug]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Full name</label>
                        <input name="full_name" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Email</label>
                        <input name="email" type="email" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="name@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Phone</label>
                        <input name="phone" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="+254...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Estate</label>
                        <select
                            name="estate_id"
                            class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200"
                            x-model="form.estate_id"
                            @change="syncHouses"
                            required
                        >
                            <option value="">Select estate</option>
                            @foreach ($estates as $estate)
                                <option value="{{ $estate->id }}">{{ $estate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">House (optional)</label>
                        <select
                            name="house_id"
                            class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200"
                            x-model="form.house_id"
                        >
                            <option value="">None</option>
                            <template x-for="house in filteredHouses" :key="house.id">
                                <option :value="house.id" x-text="house.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Resident type</label>
                        <input name="resident_type" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Owner, tenant">
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button class="px-5">Add resident</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Residents</h3>
                        <p class="text-sm text-slate-500">Search by name, estate, house, or type.</p>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <input
                            type="search"
                            x-model.debounce.200ms="filters.query"
                            class="w-full rounded-xl border-slate-200 pr-10 focus:border-emerald-400 focus:ring-emerald-200"
                            placeholder="Search residents..."
                        >
                        <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-xs">Ctrl+K</span>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Name</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Estate</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">House</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Type</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Status</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Statement</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($residents as $resident)
                                <tr
                                    class="hover:bg-slate-50/70 transition"
                                    x-data="{ search: @js(strtolower($resident->full_name.' '.($resident->estate->name ?? '').' '.($resident->house->house_code ?? '').' '.($resident->resident_type ?? '').' '.($resident->status ?? ''))) }"
                                    x-show="!filters.query || search.includes(filters.query.toLowerCase())"
                                >
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $resident->full_name }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $resident->estate->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $resident->house->house_code ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $resident->resident_type }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold border {{ $resident->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                            {{ $resident->status ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <a
                                            class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 font-semibold"
                                            href="{{ route('app.residents.statement', ['business' => $businessSlug, 'resident' => $resident->id]) }}"
                                        >
                                            View
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </td>
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
            Alpine.data('residentPage', ({ houses }) => ({
                form: { estate_id: '', house_id: '' },
                houses,
                filters: { query: '' },
                get filteredHouses() {
                    if (!this.form.estate_id) {
                        return this.houses;
                    }
                    return this.houses.filter(
                        (house) => String(house.estate_id) === String(this.form.estate_id)
                    );
                },
                syncHouses() {
                    const matches = this.filteredHouses.some(
                        (house) => String(house.id) === String(this.form.house_id)
                    );
                    if (!matches) {
                        this.form.house_id = '';
                    }
                },
            }));
        });
    </script>
</x-app-layout>
