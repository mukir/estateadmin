<?php

use App\Services\BillingService;
use App\Services\InvoiceReminderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('billing:run {--date=}', function (BillingService $billing) {
    $dateOption = $this->option('date');
    $date = $dateOption ? Carbon::parse($dateOption) : null;

    $result = $billing->runMonthlyBilling($date);

    $this->info("Billing period {$result['period']} complete. Created {$result['invoices_created']} invoices.");
})->purpose('Run monthly billing for all active businesses');

Artisan::command('billing:reminders', function (InvoiceReminderService $reminder) {
    $result = $reminder->sendReminders();
    $this->info("Prepared {$result['reminders_prepared']} reminders at {$result['run_at']}.");
})->purpose('Send invoice reminders for overdue balances');
