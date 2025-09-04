<?php

namespace App\Mail;

use App\Models\OfferLetter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferLetterNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $offerLetter;

    public function __construct(OfferLetter $offerLetter)
    {
        $this->offerLetter = $offerLetter;
    }

    public function build()
    {
        return $this->view('emails.offer-letter-notification')
                    ->subject('Letter of intent to hire at Jashma InfoSoft Pvt. Ltd.');
    }
}