@php
    $businessSlug = request()->route('business');
    $houseOptions = $houses->map(fn ($house) => [
        'id' => $house->id,
        'label' => $house->house_code,
        'estate_id' => $house->estate_id,
    ]);
    $residentOptions = $residents->map(fn ($resident) => [
        'id' => $resident->id,
        'label' => $resident->full_name,
        'estate_id' => $resident->estate_id,
    ]);
    $carryForwardEnabled = auth()->user()?->carry_forward_enabled ?? false;
    $invoiceCount = $invoices->count();
    $totalInvoiced = $invoices->sum('total_amount');
    $totalPaid = $invoices->sum('amount_paid');
    $totalBalance = $invoices->sum('balance');
    $collectionRate = $totalInvoiced > 0 ? round(($totalPaid / max($totalInvoiced, 1)) * 100) : 0;
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Invoices
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">Billing desk</h1>
                        <p class="text-emerald-50 max-w-2xl">Send invoices faster, keep residents synced to houses, and stay on top of balances.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $invoiceCount }} invoices
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            KES {{ number_format($totalInvoiced, 2) }} billed
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $collectionRate }}% collected
                        </span>
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Collections</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                {{ $totalBalance > 0 ? 'Follow up' : 'All clear' }}
                            </span>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Billed</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($totalInvoiced, 0) }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Paid</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($totalPaid, 0) }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Balance</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($totalBalance, 0) }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.payments', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Record payment
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.residents', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Update residents
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12" x-data="invoicePage({ houses: @js($houseOptions), residents: @js($residentOptions) })">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('business.partials.status')

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Generate recurring invoices</h3>
                        <p class="text-sm text-slate-500">Uses default service charges on houses plus estate charges.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('app.invoices.run', ['business' => $businessSlug]) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Billing period</label>
                        <input name="billing_period" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="2025-12" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Invoice date</label>
                        <input name="invoice_date" type="date" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Due date</label>
                        <input name="due_date" type="date" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Status</label>
                        <select name="status" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                            <option value="draft">Draft</option>
                            <option value="sent">Sent + email</option>
                        </select>
                    </div>
                    @if ($carryForwardEnabled)
                        <label class="flex items-start gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="carry_forward" value="1" class="mt-1 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>
                                Bring forward any open balance from the previous invoice for each resident and close the older invoice automatically.
                            </span>
                        </label>
                    @else
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                            Arrears carry-forward is disabled for your account. Enable it in Profile settings to use on recurring invoices.
                        </div>
                    @endif
                    <div class="md:col-span-4 flex justify-end">
                        <x-primary-button class="px-5">Generate invoices</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Quick invoice</h3>
                        <p class="text-sm text-slate-500">Estate selection filters houses and residents automatically.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                        New bill
                    </span>
                </div>
                <form method="POST" action="{{ route('app.invoices.store', ['business' => $businessSlug]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Estate</label>
                        <select
                            name="estate_id"
                            class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200"
                            x-model="form.estate_id"
                            @change="syncForEstate"
                            required
                        >
                            <option value="">Select estate</option>
                            @foreach ($estates as $estate)
                                <option value="{{ $estate->id }}">{{ $estate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">House</label>
                        <select
                            name="house_id"
                            class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200"
                            x-model="form.house_id"
                            required
                        >
                            <option value="">Select house</option>
                            <template x-for="house in filteredHouses" :key="house.id">
                                <option :value="house.id" x-text="house.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Resident</label>
                        <select
                            name="resident_id"
                            class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200"
                            x-model="form.resident_id"
                            required
                        >
                            <option value="">Select resident</option>
                            <template x-for="resident in filteredResidents" :key="resident.id">
                                <option :value="resident.id" x-text="resident.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Billing period</label>
                        <input name="billing_period" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="2025-11" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Invoice date (optional)</label>
                        <input name="invoice_date" type="date" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Due date (optional)</label>
                        <input name="due_date" type="date" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Description</label>
                        <input name="description" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Rent, service charge" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Amount</label>
                        <input name="amount" type="number" step="0.01" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Status</label>
                        <select name="status" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700 mt-2">
                        <input type="checkbox" name="send_now" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Email invoice with PDF attachment
                    </label>
                    @if ($carryForwardEnabled)
                        <label class="flex items-start gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="carry_forward" value="1" class="mt-1 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>
                                Bring forward any open balance from the previous invoice for this resident and close the old invoice.
                                If found, the arrears amount is added as a line item here.
                            </span>
                        </label>
                    @else
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                            Arrears carry-forward is disabled for your account. Enable it in Profile settings to use on new invoices.
                        </div>
                    @endif
                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button class="px-5">Create invoice</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Recent invoices</h3>
                        <p class="text-sm text-slate-500">Search by period, resident, or unit.</p>
                    </div>
                    <input
                        type="search"
                        x-model.debounce.200ms="filters.query"
                        class="w-full sm:w-72 rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200"
                        placeholder="Search invoices..."
                    >
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Reference</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Period</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">House</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Resident</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Total</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Paid</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Balance</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Status</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($invoices as $invoice)
                                <tr
                                    class="hover:bg-slate-50/70 transition"
                                    x-data="{ search: @js(strtolower($invoice->billing_period.' '.($invoice->house->house_code ?? '').' '.($invoice->resident->full_name ?? ''))) }"
                                    x-show="!filters.query || search.includes(filters.query.toLowerCase())"
                                >
                                    <td class="py-3 px-4 text-slate-700">{{ $invoice->reference }}</td>
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $invoice->billing_period }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $invoice->house->house_code ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $invoice->resident->full_name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-900">KES {{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="py-3 px-4 text-emerald-700 font-semibold">KES {{ number_format($invoice->amount_paid, 2) }}</td>
                                    <td class="py-3 px-4 text-rose-600 font-semibold">KES {{ number_format($invoice->balance, 2) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold border
                                            {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' :
                                               ($invoice->status === 'partial' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-slate-50 text-slate-700 border-slate-200') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('app.invoices.pdf', ['business' => $businessSlug, 'invoice' => $invoice]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                            PDF
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
            Alpine.data('invoicePage', ({ houses, residents }) => ({
                form: { estate_id: '', house_id: '', resident_id: '' },
                houses,
                residents,
                filters: { query: '' },
                get filteredHouses() {
                    if (!this.form.estate_id) {
                        return this.houses;
                    }
                    return this.houses.filter(
                        (house) => String(house.estate_id) === String(this.form.estate_id)
                    );
                },
                get filteredResidents() {
                    if (!this.form.estate_id) {
                        return this.residents;
                    }
                    return this.residents.filter(
                        (resident) => String(resident.estate_id) === String(this.form.estate_id)
                    );
                },
                syncForEstate() {
                    const houseMatch = this.filteredHouses.some(
                        (house) => String(house.id) === String(this.form.house_id)
                    );
                    const residentMatch = this.filteredResidents.some(
                        (resident) => String(resident.id) === String(this.form.resident_id)
                    );

                    if (!houseMatch) {
                        this.form.house_id = '';
                    }
                    if (!residentMatch) {
                        this.form.resident_id = '';
                    }
                },
            }));
        });
    </script>
</x-app-layout>
