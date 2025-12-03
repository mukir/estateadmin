<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function build(): self
    {
        $this->invoice->loadMissing(['items', 'resident', 'house', 'estate']);
        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $this->invoice]);

        return $this->subject('Invoice '.$this->invoice->reference)
            ->view('mail.invoice')
            ->with(['invoice' => $this->invoice])
            ->attachData($pdf->output(), 'invoice-'.$this->invoice->reference.'.pdf');
    }
}
