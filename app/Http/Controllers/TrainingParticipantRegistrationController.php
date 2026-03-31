<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingParticipant;
use App\Notifications\TrainingParticipantRegistered;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class TrainingParticipantRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $training = Training::where('id', $request->id)->firstOrFail();

        return Inertia::render('Training/ParticipantsRegistration', ['training' => $training]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $request->validate([
            'training_id' => 'required|string',
            'lname' => 'required|string|max:50',
            'fname' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) use ($request) {
                    $normalized = strtolower(trim((string) $value));
                    $exists = TrainingParticipant::query()
                        ->where('training_id', $request->training_id)
                        ->whereRaw('LOWER(TRIM(COALESCE(email, ""))) = ?', [$normalized])
                        ->exists();

                    if ($exists) {
                        $fail(__('This email is already registered for this training.'));
                    }
                },
            ],
            'is_internal' => 'required|boolean',
            'is_female' => 'required|boolean',
        ]);

        $training = Training::query()->where('id', $request->training_id)->firstOrFail();

        $p = new TrainingParticipant();

        $p->training_id = $request->training_id;
        $p->lname = strtolower($request->lname);
        $p->mname = strtolower($request->mname);
        $p->fname = strtolower($request->fname);
        $p->ext_name = strtolower($request->ext_name);
        $p->is_internal = $request->is_internal;
        $p->email = $request->email;
        $p->position = $request->position;
        $p->is_female = $request->is_female;

        $p->save();

        try {
            Notification::route('mail', $p->email)
                ->notify(new TrainingParticipantRegistered($p, $training));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->back();
    }

    public function registrationSent(){
        return Inertia::render('Training/ParticipantsRegistrationSent', []);
    }
}
