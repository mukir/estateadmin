<?php

namespace App\Services;

use App\Models\Estate;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function arrears(): Collection
    {
        return Invoice::with(['estate', 'house', 'resident'])
            ->where('balance', '>', 0)
            ->orderByDesc('due_date')
            ->get();
    }

    public function payments(?string $from = null, ?string $to = null): Collection
    {
        return Payment::with(['invoice.resident', 'invoice.house'])
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', Carbon::parse($from)))
            ->when($to, fn ($q) => $q->whereDate('payment_date', '<=', Carbon::parse($to)))
            ->orderByDesc('payment_date')
            ->get();
    }

    public function collectionsSummary(?string $from = null, ?string $to = null): array
    {
        $payments = $this->payments($from, $to);

        return [
            'total' => $payments->sum('amount'),
            'by_method' => $payments->groupBy('method')->map->sum('amount'),
            'payments' => $payments,
        ];
    }

    public function occupancy(): Collection
    {
        return Estate::withCount([
            'houses as occupied_units_count' => fn ($q) => $q->where('is_occupied', true),
            'houses as planned_units_count' => fn ($q) => $q,
        ])->get();
    }

    public function invoiceStatus(): Collection
    {
        return Invoice::with(['resident', 'house', 'estate'])
            ->orderByDesc('invoice_date')
            ->get();
    }
}
