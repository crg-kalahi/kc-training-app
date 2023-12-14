<?php

namespace App\Http\Controllers;

use App\Exports\EvaluationExport;
use App\Exports\TrainingParticipantExport;
use App\Mail\SendEmail;
use App\Models\Conf\KeyResourcePerson;
use App\Models\Conf\KeyTraining;
use App\Models\Conf\Learning;
use App\Models\Conf\OfficeRepresentative;
use App\Models\EvaluationKeyArea;
use App\Models\EvaluationKeyLearning;
use App\Models\EvaluationKeyResourcePerson;
use App\Models\EvaluationTraining;
use App\Models\EventFacilitator;
use App\Models\Training;
use App\Models\TrainingResourcePerson;
use App\Models\User;
use App\Notifications\SendEmail as NotificationsSendEmail;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TrainingController extends Controller
{
   
    public function PublicEvaluationForm($id){
        $item = Training::where('id', $id)->firstOrFail();

        $givenDateTime = new DateTime($item->date_to);
        $currentDateTime = new DateTime();

        $givenDateTime->setTime(0, 0, 0);
        $currentDateTime->setTime(0, 0, 0);
        if($givenDateTime != $currentDateTime){
            abort(403, "Training was already finished and can't evaluate today.");
        }
        return Inertia::render('Training/EvaluationPublic', [
            'training' => $item,
            'officeRep' => OfficeRepresentative::get(),
            'keyTraining' => KeyTraining::whereIn('id', explode(',', $item->key_trainings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyRP' => KeyResourcePerson::whereIn('id', explode(',', $item->key_rp))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyLearning' => Learning::whereIn('id', explode(',', $item->key_learnings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'resourcePerson' => $item->resourcePersons,
        ]);
    }

    public function PublicEvaluationFormStore(Request $request){
        $request->validate([
            'key_training' => 'required|array',
            'key_RP' => 'required|array',
            'training_id' => 'required|string',
            'office_rep' => 'required|integer',
            'sex' => 'required|string'
        ]);

        $training = Training::findOrFail($request->training_id);

        $givenDateTime = new DateTime($training->date_to);
        $currentDateTime = new DateTime();
        $givenDateTime->setTime(0, 0, 0);
        $currentDateTime->setTime(0, 0, 0);
        if($givenDateTime != $currentDateTime){
            abort(403, "Training was already finished and can't evaluate today.");
        }

        $eval = EvaluationTraining::create([
            'training_id' => $request->training_id,
            'sex' => $request->sex,
            'office_rep_id' => $request->office_rep,
        ]);

        $keyTraining = [];
        foreach($request->key_training as $key){
            $item = new EvaluationKeyArea([
                'stat' => $key['stat'],
                'area_training_id' => $key['id']
            ]);
            $keyTraining[] = $item;
        }
        $eval->keyTraining()->saveMany($keyTraining);

        $keyRP = [];
        foreach($request->key_RP as $rp){
            foreach($rp['key_rp'] as $key){
                $item = new EvaluationKeyResourcePerson([
                    'stat' => $key['stat'],
                    'area_rp_id' => $key['id'],
                    'rp_id' => $rp['id']
                ]);
                $keyRP[] = $item;
            }
        }
        $eval->keyResourcePerson()->saveMany($keyRP);
        
        $keyLearning = [];
        foreach($request->key_learning as $key){
            $item = new EvaluationKeyLearning([
                'answer' => $key['answer'],
                'learning_id' => $key['id']
            ]);
            $keyLearning[] = $item;
        }

        $training = Training::find($request->training_id);
        //Email Notification
        $project = [
            'greeting' => 'Hi '.$request->f_name.',',
            'body' => 'This is the certificate of participation on '. $training->title,
            'thanks' => 'Thank you this is from Capacity Building Web Application',
            'actionText' => 'Download Certificate',
            'actionURL' => route('public.cert.participant', [
                'l_name' => $request->l_name,
                'f_name' => $request->f_name,
                'm_name' => $request->m_name,
                'ext_name' => $request->ext_name,
                'training_id' => $request->training_id,
            ]),
        ];

        // Mail::to('jolan.owang@gmail.com')->send(new SendEmail($project));
        Notification::route('mail', $request->email)->notify(new NotificationsSendEmail($project));

        $eval->keyLearning()->saveMany($keyLearning);
        return redirect()->back();
        // return view('thanks');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pagination = Training::with('facilitators')->orderBy('date_from', 'desc')->paginate(20);
        return Inertia::render('Training/Index', ['pagination' => $pagination]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Training/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'date_from' => 'required|string|max:255',
            'date_to' => 'required|string|max:255',
        ]);
        
        $default_key_training = collect(KeyTraining::where('is_default', true)->get())->implode("id", ",");
        $default_key_learning = collect(Learning::where('is_default', true)->get())->implode("id", ",");
        $default_key_rp = collect(KeyResourcePerson::where('is_default', true)->get())->implode("id", ",");

        $new = Training::create([
            'title' => $request->title,
            'venue' => $request->venue,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'encoded_by' => Auth::id(),
            'key_trainings' => $default_key_training,
            'key_learnings' => $default_key_learning,
            'key_rp' => $default_key_rp
        ]);

        return Redirect::route('training.edit', [$new->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function show(Training $training)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function edit(Training $training)
    {
        $training->load('encodedBy');
        $training->load('facilitators_');
        $training->load('resourcePersons');
        $users = User::get();
        return Inertia::render('Training/Edit', 
            [   'training' => $training,
                'users' => $users,
                'listTraining' => KeyTraining::get(),
                'listLearning' => Learning::get(),
                'listKeyResourcePerson' => KeyResourcePerson::get(), 
            ]
        );

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Training $training)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'date_from' => 'required|string|max:255',
            'date_to' => 'required|string|max:255',
        ]);

        $training->title = $request->title;
        $training->venue = $request->venue;
        $training->date_from = $request->date_from;
        $training->date_to = $request->date_to;
        if($training->isDirty()){
            $training->save();
        }
        
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $t = Training::findOrFail($id);
        $t->delete();
        return Redirect::route('training.index');
    }

    /**
     * Facilitator
     */
    public function facilitateFacilitator(Request $request){
        $request->validate([
            'id' => 'required|string'
        ]);
        $training = Training::find($request->id);
        $items = DB::table('event_facilitators')->whereIn('user_id', $request->id_numbers)->get();
        if(!count($items)){
            $container = [];
            foreach($request->id_numbers as $id){
                $container[] = [
                    'user_id' => $id,
                ];
            }
            $training->facilitators_()->createMany($container);  
            return redirect()->back();
        }
        // Delete All
        $training->facilitators_()->delete();
        foreach($request->id_numbers as $id){
            $f = EventFacilitator::withTrashed()
                ->where('training_id', $training->id)
                ->where('user_id', $id)->first();
            if($f){
                $f->restore();
            }else{
                EventFacilitator::create([
                    'training_id' => $training->id,
                    'user_id' => $id
                ]);
            }
        }
        return redirect()->back();
    }
    
    public function UpdateKeyFactors($id, Request $request){
        $training = Training::findOrFail($id);
        $training->key_trainings = $request->key_training ? implode(",", $request->key_training) : null;
        $training->key_learnings = $request->key_learning ? implode(",", $request->key_learning) : null;
        $training->key_rp = $request->key_rp ? implode(",", $request->key_rp) : null;
        $training->save();
        return redirect()->back();
    }
    
    public function UpdateResourcePerson(Request $request){
        $request->validate([
            'training_id' => 'required|string',
            'lname' => 'required|string|max:50',
            'fname' => 'required|string|max:50',
            'is_internal' => 'required|boolean',
            'topic' => 'required|string',
        ]);

        if(!$request->id){
            TrainingResourcePerson::create([
                'training_id' => $request->training_id,
                'lname' => strtolower($request->lname),
                'mname' => strtolower($request->mname),
                'fname' => strtolower($request->fname),
                'ext_name' => strtolower($request->ext_name),
                'is_internal' => $request->is_internal,
                'position' => $request->position,
                'is_female' => $request->is_female,
                'topic' => $request->topic,
                'encoded_by' => Auth::id()
            ]);
            return redirect()->back();
        }
        
        $rp = TrainingResourcePerson::findOrFail($request->id);
        $rp->training_id = $request->training_id;
        $rp->lname = strtolower($request->lname);
        $rp->mname = strtolower($request->mname);
        $rp->fname = strtolower($request->fname);
        $rp->ext_name = strtolower($request->ext_name);
        $rp->is_internal = $request->is_internal;
        $rp->is_female = $request->is_female;
        $rp->topic = $request->topic;
        $rp->save();
        
        return redirect()->back();
    }

    public function RemoveResourcePerson(Request $request){
        $request->validate([
            'id' => 'required|string'
        ]);
        $rp = TrainingResourcePerson::findOrFail($request->id);
        $rp->delete();
        return redirect()->back();
    }

    public function TGetParticipants($id){
        $item = Training::where('id', $id)->firstOrFail();
        return Inertia::render('Training/Participants', ['training' => $item, 'people' => $item->participants]);
    }
    public function GetEvaluations($id){
        $item = Training::where('id', $id)->firstOrFail();
        $participants = collect($item->evaluations);
        $totalMale = count($participants
            ->filter(function($key){
                return $key->sex != '0' && $key->sex == 'male' ? true : false;
            }));
        $eval = $item->evaluations;
        // $item->load('evaluation_status')
        return Inertia::render('Training/Evaluations', ['training' => $item,
            'officeRep' => OfficeRepresentative::get(),
            'keyTraining' => KeyTraining::whereIn('id', explode(',', $item->key_trainings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyRP' => KeyResourcePerson::whereIn('id', explode(',', $item->key_rp))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyLearning' => Learning::whereIn('id', explode(',', $item->key_learnings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'resourcePerson' => $item->resourcePersons,
            'totalMale' => $totalMale,
            'totalFemale' => abs($totalMale - count($eval->filter(function($key){ return $key->sex != '0'; }))),
            'totalPreferNotToSay' => count($participants->filter(function($key){ return $key->sex == '0'; })),
            'overallRating' => $item->evaluation_status,
        ]);
    }

    public function GetEvaluationResponse($id){
        $item = Training::where('id', $id)->firstOrFail();
        
        return Inertia::render('Training/EvaluationResponse', [
            'training' => $item,
            'officeRep' => OfficeRepresentative::get(),
            'keyTraining' => KeyTraining::whereIn('id', explode(',', $item->key_trainings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyRP' => KeyResourcePerson::whereIn('id', explode(',', $item->key_rp))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyLearning' => Learning::whereIn('id', explode(',', $item->key_learnings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'evaluations' => $item->evaluations,
            'resourcePerson' => $item->resourcePersons,
        ]);
    }

    public function ShowEvaluationResponse($id){
        $item = EvaluationTraining::where('id', $id)->firstOrFail();
        
        return response()->json([
            'e_key_areas' => $item->keyTraining,
            'e_key_learning' => $item->keyLearning,
            'e_key_rp' => $item->keyResourcePerson,
        ]);
    }

    public function UpdateEvaluationResponse($id, Request $request){
        $item = EvaluationTraining::where('id', $id)->firstOrFail();
        $item->sex = $request->sex;
        $item->office_rep_id = $request->office_rep_id;
        if($item->isDirty()){
            $item->save();
        }

        foreach($request->areas as $area){
            DB::table('evaluation_key_areas')
                ->where('id', $area['id'])
                ->update(['stat' => $area['stat']]);
        }
        foreach($request->learnings as $learning){
            DB::table('evaluation_key_learnings')
                ->where('id', $learning['id'])
                ->update(['answer' => $learning['answer']]);
        }
        foreach($request->rp as $rp){
            DB::table('evaluation_key_resource_people')
                ->where('id', $rp['id'])
                ->update(['stat' => $rp['stat']]);
        }
        
        return response()->json(['message' => 'successfully updated!']);
    }
    
    public function RemoveEvaluationResponse($id){
        $item = EvaluationTraining::where('id', $id)->firstOrFail();
        $item->keyTraining()->delete();
        $item->keyLearning()->delete();
        $item->keyResourcePerson()->delete();
        $item->delete();
        
        return response()->json(['message' => 'successfully removed!']);
    }

    public function StoreEvaluation(Request $request){
        $request->validate([
            'key_training' => 'required|array',
            'key_RP' => 'required|array',
            'training_id' => 'required|string',
            'office_rep' => 'required|integer',
            'sex' => 'required|string'
        ]);

        $eval = EvaluationTraining::create([
            'training_id' => $request->training_id,
            'sex' => $request->sex,
            'office_rep_id' => $request->office_rep,
        ]);

        $keyTraining = [];
        foreach($request->key_training as $key){
            $item = new EvaluationKeyArea([
                'stat' => $key['stat'],
                'area_training_id' => $key['id']
            ]);
            $keyTraining[] = $item;
        }
        $eval->keyTraining()->saveMany($keyTraining);

        $keyRP = [];
        foreach($request->key_RP as $rp){
            foreach($rp['key_rp'] as $key){
                $item = new EvaluationKeyResourcePerson([
                    'stat' => $key['stat'],
                    'area_rp_id' => $key['id'],
                    'rp_id' => $rp['id']
                ]);
                $keyRP[] = $item;
            }
        }
        $eval->keyResourcePerson()->saveMany($keyRP);
        
        $keyLearning = [];
        foreach($request->key_learning as $key){
            $item = new EvaluationKeyLearning([
                'answer' => $key['answer'],
                'learning_id' => $key['id']
            ]);
            $keyLearning[] = $item;
        }

        $eval->keyLearning()->saveMany($keyLearning);
        return redirect()->back();
    }

    public function ExportTrainingReport($id){
        $training = Training::where('id', $id)->firstOrFail();
        return (new EvaluationExport($training->id))->download($training->id.'.xlsx');
    }

    public function ExportTrainingParticipants($id){
        $training = Training::where('id', $id)->firstOrFail();
        // return (new TrainingParticipantExport($training->id))->download($training->id.'.xlsx');
        return Excel::download(new TrainingParticipantExport($training->id), $training->id.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }
    
    public function PreviewTrainingReport($id){
        $training = Training::where('id', $id)->firstOrFail();
        $training->load('facilitators');
        return Inertia::render('Training/PreviewEvaluationReport', [
            'key_rp' => KeyResourcePerson::whereIn('id', explode(',', $training->key_rp))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'key_training' => KeyTraining::whereIn('id', explode(',', $training->key_trainings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'key_learning' => Learning::whereIn('id', explode(',', $training->key_learnings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'items_rp' => DB::table('evaluation_resource_person_view')->where('training_id', $training->id)->orderBy('stat', 'desc')->orderBy('area_rp_id', 'desc')->get(),
            'items_learning' => DB::table('evaluation_learning_view')->whereNotNull('answer')->where('training_id', $training->id)->orderBy('learning_id')->get(),
            'item_training' => DB::table('evaluation_training_view')->where('training_id', $training->id)->orderBy('stat', 'desc')->get(),
            'training' => $training,
        ]);
    }
}
