<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Interview;

class InterviewRejectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $hrName;
    public $rejectionType;

    public function __construct(Interview $interview, $hrName, $rejectionType)
    {
        $this->interview = $interview;
        $this->hrName = $hrName;
        $this->rejectionType = $rejectionType;
    }

    public function envelope(): Envelope
    {
        $subject = match($this->rejectionType) {
            'no_position' => 'Application Status Update at Jashma InfoSoft Pvt. Ltd.',
            'high_ctc' => 'High CTC expectation - Rejection email',
            'after_interview' => 'Application Status Update at Jashma InfoSoft Pvt. Ltd.',
            default => 'Application Status Update at Jashma InfoSoft Pvt. Ltd.'
        };
        
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.interview-rejection',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
