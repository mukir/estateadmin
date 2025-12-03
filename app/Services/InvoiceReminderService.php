<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Invoice;
use App\Support\BusinessContext;
use Illuminate\Support\Facades\Log;

class InvoiceReminderService
{
    public function sendReminders(): array
    {
        $now = now()->toDateString();
        $reminded = 0;

        Business::where('status', '!=', 'suspended')
            ->get()
            ->each(function (Business $business) use ($now, &$reminded) {
                BusinessContext::set($business);

                $overdueInvoices = Invoice::whereIn('status', ['sent', 'partial'])
                    ->where('balance', '>', 0)
                    ->whereDate('due_date', '<', $now)
                    ->get();

                foreach ($overdueInvoices as $invoice) {
                    $invoice->status = 'overdue';
                    $invoice->saveQuietly();
                    $reminded++;

                    Log::info('Invoice reminder ready', [
                        'business_id' => $business->id,
                        'invoice_id' => $invoice->id,
                        'resident_id' => $invoice->resident_id,
                        'balance' => $invoice->balance,
                    ]);
                }
            });

        BusinessContext::forget();

        return [
            'reminders_prepared' => $reminded,
            'run_at' => $now,
        ];
    }
}
