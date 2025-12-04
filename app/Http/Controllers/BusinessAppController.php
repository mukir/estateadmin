<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMailable;
use App\Mail\PaymentReceiptMailable;
use App\Models\Business;
use App\Models\FollowUp;
use App\Models\Estate;
use App\Models\House;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\ServiceCharge;
use App\Support\Audit;
use App\Support\BusinessContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class BusinessAppController extends Controller
{
    public function estates()
    {
        $estates = Estate::orderBy('name')->get();

        return view('business.estates', compact('estates'));
    }

    public function storeEstate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Estate::create($data);

        return back()->with('status', 'Estate created.');
    }

    public function houses()
    {
        $estates = Estate::orderBy('name')->get();
        $houses = House::with('estate')->orderBy('house_code')->get();

        return view('business.houses', compact('estates', 'houses'));
    }

    public function storeHouse(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'estate_id' => ['required', 'exists:estates,id'],
            'house_code' => ['required', 'string', 'max:100'],
            'house_type' => ['nullable', 'string', 'max:100'],
            'block' => ['nullable', 'string', 'max:100'],
            'default_service_charge' => ['nullable', 'numeric', 'min:0'],
        ]);

        House::create($data);

        return back()->with('status', 'House created.');
    }

    public function residents()
    {
        $estates = Estate::orderBy('name')->get();
        $houses = House::orderBy('house_code')->get();
        $residents = Resident::with(['estate', 'house'])->orderBy('full_name')->get();

        return view('business.residents', compact('estates', 'houses', 'residents'));
    }

    public function storeResident(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'estate_id' => ['required', 'exists:estates,id'],
            'house_id' => ['nullable', Rule::exists('houses', 'id')->where('estate_id', $request->estate_id)],
            'resident_type' => ['nullable', 'string', 'max:50'],
        ]);

        $resident = Resident::create($data);
        if ($data['house_id'] ?? null) {
            $resident->attachToHouse(House::find($data['house_id']));
        }

        return back()->with('status', 'Resident added.');
    }

    public function serviceCharges()
    {
        $estates = Estate::orderBy('name')->get();
        $charges = ServiceCharge::with('estate')->orderBy('estate_id')->orderBy('name')->get();

        return view('business.service-charges', compact('estates', 'charges'));
    }

    public function storeServiceCharge(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'estate_id' => ['required', 'exists:estates,id'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string', 'max:50'],
        ]);

        ServiceCharge::create($data);

        return back()->with('status', 'Service charge created.');
    }

    public function invoices()
    {
        $estates = Estate::orderBy('name')->get();
        $houses = House::orderBy('house_code')->get();
        $residents = Resident::orderBy('full_name')->get();
        $invoices = Invoice::with(['house', 'resident'])
            ->orderByDesc('invoice_date')
            ->limit(50)
            ->get();

        return view('business.invoices', compact('estates', 'houses', 'residents', 'invoices'));
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'estate_id' => ['required', 'exists:estates,id'],
            'house_id' => ['required', Rule::exists('houses', 'id')->where('estate_id', $request->estate_id)],
            'resident_id' => ['required', Rule::exists('residents', 'id')->where('estate_id', $request->estate_id)],
            'billing_period' => ['required', 'string', 'max:20'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'invoice_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['draft', 'sent'])],
            'send_now' => ['nullable', 'boolean'],
        ]);

        $invoice = Invoice::create([
            'estate_id' => $data['estate_id'],
            'house_id' => $data['house_id'],
            'resident_id' => $data['resident_id'],
            'billing_period' => $data['billing_period'],
            'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
            'due_date' => $data['due_date'] ?? now()->addDays(7)->toDateString(),
            'status' => $data['status'] ?? 'draft',
        ]);

        InvoiceItem::create([
            'business_id' => BusinessContext::id(),
            'invoice_id' => $invoice->id,
            'description' => $data['description'],
            'amount' => $data['amount'],
            'quantity' => 1,
        ]);

        $invoice->recalculateTotals();
        $invoice->generateReference();

        if (($data['send_now'] ?? false) || ($data['status'] ?? null) === 'sent') {
            $invoice->markSent();
            $this->sendInvoiceEmail($invoice);
            Audit::log('invoice_sent', 'Invoice emailed', [
                'invoice_id' => $invoice->id,
                'resident_id' => $invoice->resident_id,
            ]);
        }

        return back()->with('status', 'Invoice created.');
    }

    public function runRecurringInvoices(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'billing_period' => ['required', 'string', 'max:20'],
            'invoice_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['draft', 'sent'])],
        ]);

        $invoiceDate = $data['invoice_date'] ?? now()->toDateString();
        $dueDate = $data['due_date'] ?? now()->addDays(7)->toDateString();
        $status = $data['status'] ?? 'draft';

        $created = 0;

        $houses = House::with(['estate', 'residents'])->get();
        foreach ($houses as $house) {
            $resident = $house->currentResident();
            if (! $resident) {
                continue;
            }

            $existing = Invoice::where([
                'estate_id' => $house->estate_id,
                'house_id' => $house->id,
                'resident_id' => $resident->id,
                'billing_period' => $data['billing_period'],
            ])->first();

            if ($existing) {
                continue;
            }

            $invoice = Invoice::create([
                'estate_id' => $house->estate_id,
                'house_id' => $house->id,
                'resident_id' => $resident->id,
                'billing_period' => $data['billing_period'],
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'status' => $status,
            ]);

            if ($house->default_service_charge > 0) {
                InvoiceItem::create([
                    'business_id' => BusinessContext::id(),
                    'invoice_id' => $invoice->id,
                    'description' => 'Service charge - '.$house->house_code,
                    'amount' => $house->default_service_charge,
                    'quantity' => 1,
                ]);
            }

            $charges = ServiceCharge::where('estate_id', $house->estate_id)->where('is_active', true)->get();
            foreach ($charges as $charge) {
                InvoiceItem::create([
                    'business_id' => BusinessContext::id(),
                    'invoice_id' => $invoice->id,
                    'service_charge_id' => $charge->id,
                    'description' => $charge->name,
                    'amount' => $charge->amount,
                    'quantity' => 1,
                ]);
            }

            $invoice->recalculateTotals();
            $invoice->generateReference();

            if ($status === 'sent') {
                $invoice->markSent();
                $this->sendInvoiceEmail($invoice);
                Audit::log('invoice_sent', 'Recurring invoice emailed', [
                    'invoice_id' => $invoice->id,
                    'resident_id' => $invoice->resident_id,
                    'billing_period' => $invoice->billing_period,
                ]);
            }

            $created++;
        }

        if ($created === 0) {
            return back()->with('status', 'No new invoices created for this period.');
        }

        return back()->with('status', "Created {$created} recurring invoice(s).");
    }

    public function payments()
    {
        $invoices = Invoice::with(['resident', 'house'])->orderByDesc('invoice_date')->get();
        $payments = Payment::with('invoice')->orderByDesc('payment_date')->limit(50)->get();

        return view('business.payments', compact('invoices', 'payments'));
    }

    public function storePayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);

        $invoice->payments()->create([
            'payment_date' => $data['payment_date'],
            'amount' => $data['amount'],
            'method' => $data['method'],
            'reference' => $data['reference'] ?? 'RCPT-'.now()->format('ymd').'-'.substr(uniqid(), -4),
            'status' => 'confirmed',
        ]);

        $invoice->refresh();
        $invoice->recalculateTotals();

        if ($invoice->resident && $invoice->resident->email) {
            Mail::to($invoice->resident->email)->send(new PaymentReceiptMailable($invoice->fresh()));
        }

        Audit::log('payment_recorded', 'Payment posted', [
            'invoice_id' => $invoice->id,
            'resident_id' => $invoice->resident_id,
            'amount' => $data['amount'],
            'method' => $data['method'],
        ]);

        return back()->with('status', 'Payment recorded and receipt issued.');
    }

    public function invoicePdf(Business $business, Invoice $invoice)
    {
        if ($invoice->business_id !== $business->id) {
            abort(404);
        }

        $invoice->load(['resident', 'house', 'estate', 'items']);
        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);

        $filename = 'invoice-'.$invoice->reference.'.pdf';
        return $pdf->download($filename);
    }

    protected function sendInvoiceEmail(Invoice $invoice): void
    {
        if (! $invoice->resident?->email) {
            return;
        }

        Mail::to($invoice->resident->email)->send(new InvoiceMailable($invoice));
    }

    public function reports()
    {
        $arrears = Invoice::with(['estate', 'house', 'resident'])
            ->where('balance', '>', 0)->orderByDesc('due_date')->get();

        $payments = Payment::with('invoice.house')->orderByDesc('payment_date')->limit(50)->get();

        $occupancy = Estate::withCount([
            'houses as occupied_units_count' => fn ($q) => $q->where('is_occupied', true),
            'houses as planned_units_count' => fn ($q) => $q,
        ])->get();

        $aging = $this->buildAgingBuckets($arrears);
        $followUps = FollowUp::with(['resident', 'invoice'])->latest()->limit(20)->get();
        $residents = Resident::orderBy('full_name')->get();
        $openInvoices = Invoice::with(['resident'])->where('balance', '>', 0)->orderByDesc('due_date')->get();

        return view('business.reports', compact('arrears', 'payments', 'occupancy', 'aging', 'followUps', 'residents', 'openInvoices'));
    }

    public function residentStatement(Resident $resident)
    {
        $invoices = $resident->invoices()->with(['items', 'payments', 'house', 'estate'])->orderBy('invoice_date')->get();
        $payments = $resident->payments()->orderBy('payment_date')->get();

        $runningBalance = 0;
        $statement = [];

        foreach ($invoices as $invoice) {
            $runningBalance += $invoice->total_amount;
            $statement[] = [
                'type' => 'invoice',
                'date' => $invoice->invoice_date,
                'reference' => $invoice->billing_period,
                'amount' => $invoice->total_amount,
                'balance' => $runningBalance,
            ];
        }

        foreach ($payments as $payment) {
            $runningBalance -= $payment->amount;
            $statement[] = [
                'type' => 'payment',
                'date' => $payment->payment_date,
                'reference' => $payment->reference,
                'amount' => $payment->amount * -1,
                'balance' => $runningBalance,
            ];
        }

        usort($statement, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return view('business.resident-statement', [
            'resident' => $resident,
            'statement' => $statement,
            'runningBalance' => $runningBalance,
        ]);
    }

    public function storeFollowUp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'channel' => ['required', 'string', 'max:50'],
            'status_tag' => ['nullable', 'string', 'max:50'],
            'next_action_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        FollowUp::create([
            'resident_id' => $data['resident_id'],
            'invoice_id' => $data['invoice_id'] ?? null,
            'user_id' => auth()->id(),
            'channel' => $data['channel'],
            'status_tag' => $data['status_tag'] ?? null,
            'next_action_date' => $data['next_action_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('status', 'Follow-up logged.');
    }

    protected function buildAgingBuckets($arrears)
    {
        $buckets = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
        ];

        foreach ($arrears as $invoice) {
            $days = now()->diffInDays($invoice->due_date, false) * -1;
            if ($days <= 30) {
                $buckets['0-30'] += $invoice->balance;
            } elseif ($days <= 60) {
                $buckets['31-60'] += $invoice->balance;
            } elseif ($days <= 90) {
                $buckets['61-90'] += $invoice->balance;
            } else {
                $buckets['90+'] += $invoice->balance;
            }
        }

        return $buckets;
    }
}
