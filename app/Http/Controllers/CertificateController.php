<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function participation(Request $request){
        $training = Training::findOrFail($request->training_id);
        
        $mname = $request->m_name ? " ".$request->m_name."." : "";
        $extname = $request->ext_name ? ", ".$request->ext_name:"";
        $fullname = $request->f_name.$mname." ".$request->l_name.$extname;

        $title = $training->title;
        $venue = $training->venue;
        
        $data = [
            'fullname' => $fullname,
            'title' => $title,
            'venue' => $venue,
        ];

        
        if(strcmp($training->date_from, $training->date_to) == 0){
            $d_from = new DateTime($training->date_from);
            $date = $d_from->format('F d, Y');
        }else{
            $d_from = new DateTime($training->date_from);
            $d_to = new DateTime($training->date_to);
            $date = $d_from->format('F d, Y')." - ".$d_to->format('F d, Y');
        }

        $html ="
        <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Certificate of Participation</title>
                <style>
                    @page { margin: 0px; }
                    body { margin: 0px; }
                    .certificate {
                        text-align: center;
                        color: #333; /* Text color */
                    }
            
                    .participant-name {
                        font-size: 42px;
                        font-weight: bold;
                        margin-top: 25px;
                        text-decoration: underline;
                        font-family: Montserrat;
                        letter-spacing: 2px;
                    }

                    .cert-of-part {
                        font-family: 'Dancing Script', cursive;
                        font-size: 40px;
                        color: #2B50AA;
                        font-weight: bold;
                        letter-spacing: 2px;
                    }
                </style>
            </head>
            <body>
                <div style='position: relative; z-index: 10;'>
                    <img src='".asset('/storage/images/cert_background.jpg')."' width='100%'>
                    <img src='".asset('/storage/images/kc.jpg')."' width='35px' style='position:absolute;top:25;left:30%;'>
                    <img src='".asset('/storage/images/bbm.png')."' width='90px' style='position:absolute;top:10;left:34%;'>
                </div>
                <div style='text-align: center; margin-top: -40px; z-index: 20;'>
                    Republic of the Philippines<br>
                    Department of Social Welfare and Development<br>
                    Field Office Caraga, Capitol Site, Butuan City
                </div>
                <div style='text-align: center; margin-top: 20px'>
                    This<br/>
                    <span class='cert-of-part'>Certificate of Participation</span><br/>
                    <span>is given to</span>
                </div>
                <div class='certificate'>
                    <div class='participant-name'>".strtoupper($data['fullname'])."</div>
                    <i>for having successfully participated during the</i><br/><br/>
                    <span style='text-align:center;font-size:28px;font-weight:bold;font-family:Montserrat;'>".strtoupper($data['title'])."</span>
                    <br/><br/>
                    <span style='text-align:center;'>held on ".$date." at <br> ".$data['venue']." <br><br> Given this ".$d_to->format('jS')." day of ".$d_to->format('F, Y')."<br>
                </div>
                <div style='position: fixed; width: 100%; bottom: 0; text-align: center; padding-bottom: 30px;'>
                    <img src='".asset('/storage/images/e-sig.png')."' width='80px'>
                    <div style='font-weight:bold;font-size:20px;margin-top: -20px'>MARI-FLOR A. DOLLAGA-LIBANG</div>
                    <span style='font-size:20px;'>Regional Director</span>
                    <img src='".asset('/storage/images/insignia.jpg')."' width='100px' style='position: absolute; right: 40; top: 10'>
                </div>
            </body>
        </html>        
        ";


        $pdf = PDF::loadHtml($html)
        ->setPaper('a4', 'landscape');
        return $pdf->download('certificate-of-participation.pdf');
    }
}
