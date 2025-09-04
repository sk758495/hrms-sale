<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HrOtpVerificationMail extends Mailable
{
    use SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->view('emails.hr-otp-verification')
                    ->subject('HR System - Email Verification OTP')
                    ->with(['otp' => $this->otp]);
    }
}