<?php

namespace App\Console\Commands;

use App\Mail\ReportDigestMailable;
use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReportDigest extends Command
{
    protected $signature = 'reports:send-daily {--recipients=}';

    protected $description = 'Send daily CSV report digest to configured recipients';

    public function handle(ReportService $reports): int
    {
        $recipients = $this->option('recipients') ?? env('REPORT_RECIPIENTS');
        if (! $recipients) {
            $this->warn('No recipients configured (REPORT_RECIPIENTS).');
            return self::FAILURE;
        }

        $to = array_filter(array_map('trim', explode(',', $recipients)));
        if (empty($to)) {
            $this->warn('No valid recipients parsed.');
            return self::FAILURE;
        }

        $attachments = [
            'arrears-report.csv' => $this->csvFromCollection($reports->arrears()->map(fn ($i) => [
                'billing_period' => $i->billing_period,
                'due_date' => $i->due_date,
                'estate' => $i->estate->name ?? '',
                'house' => $i->house->house_code ?? '',
                'resident' => $i->resident->full_name ?? '',
                'balance' => $i->balance,
                'status' => $i->status,
            ])),
            'payments-report.csv' => $this->csvFromCollection($reports->payments()->map(fn ($p) => [
                'payment_date' => $p->payment_date,
                'amount' => $p->amount,
                'method' => $p->method,
                'invoice_period' => $p->invoice->billing_period ?? '',
                'resident' => $p->invoice->resident->full_name ?? '',
                'house' => $p->invoice->house->house_code ?? '',
            ])),
            'invoice-status-report.csv' => $this->csvFromCollection($reports->invoiceStatus()->map(fn ($i) => [
                'reference' => $i->reference,
                'billing_period' => $i->billing_period,
                'invoice_date' => $i->invoice_date,
                'due_date' => $i->due_date,
                'resident' => $i->resident->full_name ?? '',
                'house' => $i->house->house_code ?? '',
                'status' => $i->status,
                'balance' => $i->balance,
            ])),
        ];

        $body = "Daily reports digest\n- Arrears\n- Payments\n- Invoice status";

        foreach ($to as $email) {
            Mail::to($email)->send(new ReportDigestMailable($body, $attachments));
        }

        $this->info('Report digest sent to: '.implode(', ', $to));

        return self::SUCCESS;
    }

    protected function csvFromCollection($rows): string
    {
        $out = fopen('php://temp', 'r+');
        if ($rows->isEmpty()) {
            fputcsv($out, ['message']);
            fputcsv($out, ['No data found']);
        } else {
            fputcsv($out, array_keys($rows->first()));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $csv;
    }
}
