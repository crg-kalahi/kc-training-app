<?php

namespace App\Mail;


use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;

class TestPdfUploadMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $pdf = Pdf::loadView('emails.test', $this->data)->setPaper('a4', 'landscape');

        return $this->subject('Test PDF Email')
            ->view('emails.test') // you can use a basic view or none
            ->attachData($pdf->output(), 'test.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
