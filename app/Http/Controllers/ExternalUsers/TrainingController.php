<?php

namespace App\Http\Controllers\ExternalUsers;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\TrainingParticipant;
use App\Models\RequestCertificate;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function Index(){
     

        $training = TrainingParticipant::with('training')
        ->where('email', auth()->user()->email)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($participant) {
            // Check if a certificate exists
            $certificate = RequestCertificate::where('training_id', $participant->training_id)
                            ->where('training_participant_id',$participant->id)
                            ->where('is_approve',0)
                            ->get();
            
            foreach($certificate as $value){
                if($value && $value->is_approve == 0){ 
                    //meaning naay for approval ongoing so another request is not allowed
                    $participant->is_request_not_allowed = true;
                    break;
                }
            }

            return $participant;
        });


        $user = auth()->user();

        // Redirect if the user does not have the "guest" role
        if (!$user->hasRole('guest')) {
       
            return Inertia::render('Training/Me/Training', [
                'training' => $training
            ]);
        }else{
                 return Inertia::render('Guest/Training', [
                'training' => $training
            ]);
        }

     
    }

    public function StoreTrainingCertificateRequest(Request $request){

        $request->validate([
            'training_id' => [
                function ($attribute, $value, $fail) use ($request) {
                    $exists = RequestCertificate::where('training_id', $value)
                        ->where('training_participant_id', $request->training_participant_id)
                        ->where('is_approve', $request->is_approve)
                        ->exists();
    
                    if ($exists) {
                        $fail('This certificate request already exists.');
                    }
                },
            ],
        ]);


        RequestCertificate::create([
            'training_id' => $request->training_id,
            'training_participant_id' => $request->training_participant_id
        ]);
        // return redirect()->route('external.my-trainings');
        return Inertia::location(route('external.my-trainings'));
    }
}
