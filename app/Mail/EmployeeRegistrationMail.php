<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Interview;

class EmployeeRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $hrName;
    public $registrationLink;

    public function __construct(Interview $interview, $hrName, $registrationLink)
    {
        $this->interview = $interview;
        $this->hrName = $hrName;
        $this->registrationLink = $registrationLink;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Congratulations! Next Steps for Employee Registration - {$this->interview->position->name} Role",
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.employee-registration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}