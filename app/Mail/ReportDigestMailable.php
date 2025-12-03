<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportDigestMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $body, public array $attachments = [])
    {
    }

    public function build(): self
    {
        $mail = $this->subject('Daily Reports Digest')->view('mail.report-digest')->with(['body' => $this->body]);

        foreach ($this->attachments as $name => $content) {
            $mail->attachData($content, $name, ['mime' => 'text/csv']);
        }

        return $mail;
    }
}
