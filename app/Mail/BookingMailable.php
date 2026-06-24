<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $qrImage;

    public function __construct($booking, $qrImage)
    {
        $this->booking = $booking;
        $this->qrImage = $qrImage;
    }

    public function build()
    {
        return $this->subject('Foglalásod megerősítése - Getingo')
                    ->view('emails.booking_confirmed')
                    ->attachData($this->qrImage, 'qr-kod.png', [
                        'mime' => 'image/png',
                    ]);
    }
}