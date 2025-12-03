<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with('invoice');

        if ($request->filled('method')) {
            $query->where('method', $request->get('method'));
        }

        if ($from = $request->get('from')) {
            $query->whereDate('payment_date', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('payment_date', '<=', $to);
        }

        if ($request->wantsJson()) {
            return response()->json($query->orderByDesc('payment_date')->paginate(50));
        }

        $invoices = Invoice::with(['resident', 'house'])->orderByDesc('invoice_date')->get();
        $payments = $query->orderByDesc('payment_date')->limit(50)->get();

        return view('business.payments', compact('invoices', 'payments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'payment_date' => ['nullable', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);

        $payment = $invoice->payments()->create([
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'amount' => $data['amount'],
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
            'status' => $data['status'] ?? 'confirmed',
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice->refresh();

        return response()->json([
            'payment' => $payment,
            'invoice' => $invoice->load(['items', 'payments']),
        ], 201);
    }
}
