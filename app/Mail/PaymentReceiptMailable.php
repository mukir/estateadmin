<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function build(): self
    {
        $this->invoice->loadMissing(['resident', 'house', 'payments']);
        $payment = $this->invoice->payments()->latest('payment_date')->first();

        return $this->subject('Receipt for '.$this->invoice->reference)
            ->view('mail.receipt')
            ->with([
                'invoice' => $this->invoice,
                'payment' => $payment,
            ]);
    }
}
