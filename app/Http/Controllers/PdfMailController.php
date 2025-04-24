<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestPdfUploadMail;

class PdfMailController extends Controller
{
    public function send(){

        
        $data = [
            'name' => 'Dioame Jade C. Rendon',
            'training' => 'Community Volunteers Training on Project Implementation',
            'date' => 'January 2–3, 2025',
            'venue' => 'Almont Inland Resort, Butuan City',
            'givenDate' => '3rd day of January 2025',
        ];
        Mail::to('dioamejade@gmail.com')->send(new TestPdfUploadMail($data));
    }
}
