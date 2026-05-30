<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public int $code;
    public string $userName;

    public function __construct(string $userName, int $code)
    {
        $this->userName = $userName;
        $this->code = $code;
    }

    public function build(): static
    {
        return $this->subject('Mã xác thực email - CongThucNauan')
            ->view('emails.verification');
    }
}
