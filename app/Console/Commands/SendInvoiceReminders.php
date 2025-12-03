<?php

namespace App\Console\Commands;

use App\Mail\InvoiceReminderMailable;
use App\Support\Audit;
use App\Models\FollowUp;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminders extends Command
{
    protected $signature = 'reminders:invoices {--dry-run}';

    protected $description = 'Send email reminders for due or overdue invoices (with throttle and opt-out)';

    public function handle(): int
    {
        $now = now();
        $cutoff = $now->subDay();
        $dry = $this->option('dry-run');

        $invoices = Invoice::with(['resident'])
            ->where('balance', '>', 0)
            ->whereDate('due_date', '<=', $now->toDateString())
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_reminder_sent_at')
                    ->orWhere('last_reminder_sent_at', '<=', $cutoff);
            })
            ->get()
            ->filter(fn ($invoice) => $invoice->resident && ! $invoice->resident->reminder_opt_out && $invoice->resident->email);

        $sent = 0;
        foreach ($invoices as $invoice) {
            if ($dry) {
                $this->line("Would remind invoice {$invoice->id} ({$invoice->resident->email})");
                continue;
            }

            Mail::to($invoice->resident->email)->send(new InvoiceReminderMailable($invoice));

            $invoice->update(['last_reminder_sent_at' => now()]);

            FollowUp::create([
                'business_id' => $invoice->business_id,
                'resident_id' => $invoice->resident_id,
                'invoice_id' => $invoice->id,
                'channel' => 'auto-email',
                'status_tag' => 'reminder',
                'notes' => 'Automated reminder sent.',
                'next_action_date' => now()->addDays(3),
            ]);

            Audit::log('reminder_sent', 'Automated invoice reminder sent', [
                'invoice_id' => $invoice->id,
                'resident_id' => $invoice->resident_id,
            ]);

            $sent++;
        }

        $this->info($dry ? 'Dry run complete.' : "Reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
