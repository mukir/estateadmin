<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\House;
use App\Models\Invoice;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()->with(['items', 'payments', 'resident', 'house', 'estate']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($period = $request->get('billing_period')) {
            $query->where('billing_period', $period);
        }

        if ($estateId = $request->get('estate_id')) {
            $query->where('estate_id', $estateId);
        }

        if ($request->wantsJson()) {
            return response()->json($query->orderByDesc('invoice_date')->paginate(50));
        }

        $estates = Estate::orderBy('name')->get();
        $houses = House::orderBy('house_code')->get();
        $residents = Resident::orderBy('full_name')->get();
        $invoices = $query->orderByDesc('invoice_date')->limit(50)->get();

        return view('business.invoices', compact('estates', 'houses', 'residents', 'invoices'));
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['items', 'payments', 'resident', 'house', 'estate']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'estate_id' => ['required', 'integer', 'exists:estates,id'],
            'house_id' => ['required', 'integer', 'exists:houses,id'],
            'resident_id' => ['required', 'integer', 'exists:residents,id'],
            'billing_period' => ['required', 'string', 'max:20'],
            'invoice_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.service_charge_id' => ['nullable', 'integer'],
        ]);

        $estate = Estate::findOrFail($data['estate_id']);
        $house = House::where('estate_id', $estate->id)->findOrFail($data['house_id']);
        $resident = Resident::where('estate_id', $estate->id)->findOrFail($data['resident_id']);

        $existing = Invoice::where([
            'estate_id' => $estate->id,
            'house_id' => $house->id,
            'resident_id' => $resident->id,
            'billing_period' => $data['billing_period'],
        ])->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'billing_period' => 'Invoice already exists for this period.',
            ]);
        }

        $invoice = Invoice::create([
            'estate_id' => $estate->id,
            'house_id' => $house->id,
            'resident_id' => $resident->id,
            'billing_period' => $data['billing_period'],
            'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
            'due_date' => $data['due_date'] ?? now()->addDays(7)->toDateString(),
            'status' => $data['status'] ?? 'sent',
        ]);

        foreach ($data['items'] as $item) {
            $invoice->items()->create([
                'service_charge_id' => $item['service_charge_id'] ?? null,
                'description' => $item['description'],
                'amount' => $item['amount'],
                'quantity' => $item['quantity'] ?? 1,
            ]);
        }

        $invoice->recalculateTotals();

        return response()->json($invoice->load(['items', 'payments']), 201);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'invoice_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
        ]);

        $invoice->update($data);
        $invoice->recalculateTotals();

        return response()->json($invoice->load(['items', 'payments']));
    }
}
