<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->invoice->loadMissing(['resident', 'house', 'estate']);
    }

    public function build(): self
    {
        return $this->subject('Reminder: Invoice '.$this->invoice->reference.' is due')
            ->view('mail.reminder')
            ->with(['invoice' => $this->invoice]);
    }
}
