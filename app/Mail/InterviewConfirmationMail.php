<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Interview;

class InterviewConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $hrName;

    public function __construct(Interview $interview, $hrName)
    {
        $this->interview = $interview;
        $this->hrName = $hrName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Selection for {$this->interview->position->name} Role at Jashma InfoSoft Pvt. Ltd.",
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.interview-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
