@php
    $businessSlug = request()->route('business');
    $invoiceOptions = $invoices->map(fn ($invoice) => [
        'id' => $invoice->id,
        'label' => trim($invoice->billing_period.' - '.($invoice->house->house_code ?? '')),
    ]);
    $paymentCount = $payments->count();
    $paymentTotal = $payments->sum('amount');
    $latestPaymentDate = optional($payments->first())->payment_date;
@endphp

<x-app-layout>
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide">
                        <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                        Payments
                    </span>
                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-4xl font-bold drop-shadow-sm">Collections desk</h1>
                        <p class="text-emerald-50 max-w-2xl">Record payments quickly, keep invoice references tight, and track inflows by method.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            {{ $paymentCount }} payments
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                            KES {{ number_format($paymentTotal, 2) }} collected
                        </span>
                        @if ($latestPaymentDate)
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                                Latest {{ $latestPaymentDate }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-2xl bg-white/15 border border-white/20 backdrop-blur p-5 shadow-lg">
                        <div class="flex items-center justify-between text-sm text-emerald-50">
                            <span>Reconciliation</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 font-semibold text-white">
                                Keep invoices synced
                            </span>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Payments</p>
                                <p class="text-xl font-semibold text-white">{{ $paymentCount }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Total</p>
                                <p class="text-xl font-semibold text-white">KES {{ number_format($paymentTotal, 0) }}</p>
                            </div>
                            <div class="rounded-lg bg-white/15 px-3 py-3">
                                <p class="text-emerald-50">Open invoices</p>
                                <p class="text-xl font-semibold text-white">{{ $invoiceOptions->count() }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ $businessSlug ? route('app.invoices', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-emerald-700 shadow hover:-translate-y-0.5 transition"
                            >
                                Go to invoices
                            </a>
                            <a
                                href="{{ $businessSlug ? route('app.reports', ['business' => $businessSlug]) : '#' }}"
                                class="inline-flex items-center gap-2 rounded-lg bg-white/15 border border-white/30 px-3 py-2 text-sm font-semibold text-white hover:bg-white/25 transition"
                            >
                                Download report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative -mt-12 pb-12" x-data="paymentPage({ invoices: @js($invoiceOptions) })">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('business.partials.status')

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Record payment</h3>
                        <p class="text-sm text-slate-500">Search the invoice list as you type.</p>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <input
                            type="search"
                            x-model.debounce.200ms="filters.query"
                            class="w-full rounded-xl border-slate-200 pr-10 focus:border-emerald-400 focus:ring-emerald-200"
                            placeholder="Search invoice..."
                        >
                        <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-xs">Ctrl+K</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('app.payments.store', ['business' => $businessSlug]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Invoice</label>
                        <select
                            name="invoice_id"
                            class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200"
                            x-model="form.invoice_id"
                            required
                        >
                            <option value="">Select invoice</option>
                            <template x-for="invoice in filteredInvoices" :key="invoice.id">
                                <option :value="invoice.id" x-text="invoice.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Payment date</label>
                        <input name="payment_date" type="date" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Amount</label>
                        <input name="amount" type="number" step="0.01" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Method</label>
                        <input name="method" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Mpesa, bank, cash" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Reference (optional)</label>
                        <input name="reference" class="mt-2 block w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" placeholder="Receipt or transaction code">
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button class="px-5">Record payment</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-md border border-slate-100 p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Recent payments</h3>
                        <p class="text-sm text-slate-500">Latest 50 payments by date.</p>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr class="divide-x divide-slate-100">
                                <th class="py-3 px-4 font-semibold text-slate-700">Date</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Invoice</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Amount</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Method</th>
                                <th class="py-3 px-4 font-semibold text-slate-700">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-4 font-medium text-slate-900">{{ $payment->payment_date }}</td>
                                    <td class="py-3 px-4 text-slate-700">{{ $payment->invoice->billing_period ?? '-' }}</td>
                                    <td class="py-3 px-4 text-emerald-700 font-semibold">KES {{ number_format($payment->amount, 2) }}</td>
                                    <td class="py-3 px-4 text-slate-700 capitalize">{{ $payment->method }}</td>
                                    <td class="py-3 px-4 text-slate-500">{{ $payment->reference ?? '-' }}</td>
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
            Alpine.data('paymentPage', ({ invoices }) => ({
                form: { invoice_id: '' },
                invoices,
                filters: { query: '' },
                get filteredInvoices() {
                    if (!this.filters.query) {
                        return this.invoices;
                    }
                    const q = this.filters.query.toLowerCase();
                    return this.invoices.filter((invoice) =>
                        String(invoice.label).toLowerCase().includes(q)
                    );
                },
            }));
        });
    </script>
</x-app-layout>
