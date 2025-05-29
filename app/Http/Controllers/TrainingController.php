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
use App\Models\Attachments;
use App\Models\TrainingResourcePerson;
use App\Models\User;
use App\Notifications\SendEmail as NotificationsSendEmail;
use App\Notifications\SendEmailRPResult as NotificationsSendEmailRP;
use App\Notifications\SendEmailEvaluation as NotificationSendEmailEvaluation;
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
use App\Models\RequestCertificate;
use App\Models\TrainingParticipant;

class TrainingController extends Controller
{


    public function PublicEvaluationFormSecured($id,$participant_id){
        $item = Training::where('id', $id)->firstOrFail();

        $givenDateTime = new DateTime($item->date_to);
        $givenDateTime->setTime(0, 0, 0);

        $cutoffDate = clone $givenDateTime;
        $cutoffDate->modify('+5 days');

        $currentDateTime = new DateTime();
        $currentDateTime->setTime(0, 0, 0);

        if ($currentDateTime > $cutoffDate) {
            abort(403, "Training was already finished on {$givenDateTime->format('F j, Y')} (5 days ago) and can't be evaluated today.");
        }

        $participant = TrainingParticipant::where('training_id', $id)->where('id', $participant_id)->first();

        return Inertia::render('Training/EvaluationPublicSecured', [
            'participant' => $participant,
            'training' => $item,
            'officeRep' => OfficeRepresentative::get(),
            'keyTraining' => KeyTraining::whereIn('id', explode(',', $item->key_trainings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyRP' => KeyResourcePerson::whereIn('id', explode(',', $item->key_rp))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'keyLearning' => Learning::whereIn('id', explode(',', $item->key_learnings))->orderByRaw("CONVERT(`order`, SIGNED) ASC")->get(),
            'resourcePerson' => $item->resourcePersons,
        ]);
    }
   
   public function PublicEvaluationForm($id)
    {
        $item = Training::where('id', $id)->firstOrFail();

        $givenDateTime = new DateTime($item->date_to);
        $givenDateTime->setTime(0, 0, 0);

        $cutoffDate = clone $givenDateTime;
        $cutoffDate->modify('+5 days');

        $currentDateTime = new DateTime();
        $currentDateTime->setTime(0, 0, 0);

        if ($currentDateTime > $cutoffDate) {
            abort(403, "Training was already finished on {$givenDateTime->format('F j, Y')} (5 days ago) and can't be evaluated today.");
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

        // if($currentDateTime >= $givenDateTime){
        //     abort(403, "Training was already finished and can't evaluate today.");
        // }

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
            'actionURL' => route('public.cert.generate', [
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
   public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $certificateRequestsCount = RequestCertificate::where('is_approve', 0)->count();

        $query = Training::with('facilitators')->orderBy('date_from', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        if (!$user->hasRole('staff-admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('encoded_by', $user->id)
                ->orWhereHas('facilitators', function ($fq) use ($user) {
                    $fq->where('user_id', $user->id);
                });
            });
        }

        $pagination = $query->paginate(10)->withQueryString();

        return Inertia::render('Training/Index', [
            'pagination' => $pagination,
            'certificateRequestsCount' => $certificateRequestsCount,
            'filters' => [
                'search' => $search,
            ]
        ]);
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
        $attachments = Attachments::where('training_id', $training->id)->get();
        // var_dump($attachments);
        // die();
        
        return Inertia::render('Training/Edit', 
            [   'training' => $training,
                'training_id_selected' => $training->id,
                'users' => $users,
                'attachments' => $attachments,
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
            'email' => 'required|email',
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
                'email' => strtolower($request->email),
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
        $rp->email = strtolower($request->email);
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
            'notEvaluated' =>$item->participants->count() - ($totalMale +  abs($totalMale - count($eval->filter(function($key){ return $key->sex != '0'; }))) + count($participants->filter(function($key){ return $key->sex == '0'; }))),
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

    public function SendRPRating(Request $request){

        try {
            $results = DB::select('SELECT
            trp.fname,
            trp.mname,
            trp.lname,
            trp.email,
            trp.topic,
            t.title AS "t_title",
            t.venue AS "t_venue",
            JSON_ARRAYAGG(
                JSON_OBJECT(
                    "title", avg_stats.title,
                    "stat", format(avg_stats.avg_stat,2)
                )
            ) AS details
        FROM
            training_resource_people trp
            JOIN (
                SELECT
                    ekrp.rp_id,
                    krp.title,
                    AVG(ekrp.stat) AS avg_stat
                FROM
                    evaluation_key_resource_people ekrp
                    JOIN key_resource_people krp ON ekrp.area_rp_id = krp.id
                GROUP BY
                    ekrp.rp_id, krp.title
            ) AS avg_stats ON trp.id = avg_stats.rp_id
            JOIN trainings t ON trp.training_id = t.id
        WHERE
            t.id = "'.$request->training_id.'"
        GROUP BY
            trp.fname, trp.mname, trp.lname, trp.email, trp.topic, t.title, t.venue
        ORDER BY
            trp.lname, trp.fname, t.title
        ');



            $htmlData = [];
            foreach ($results as $result) {

            $htmlContent = '<!DOCTYPE html>
            <html>
            <head>
                <style>
                    *{
                    font-family: "Dancing Script", cursive;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        border: 1px solid #dddddd;
                        text-align: left;
                        padding: 8px;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>
                <h1>'.$result->topic.'</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Key Areas</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>';
                        $details = json_decode($result->details);
                        foreach ($details as $index => $detail) {
                            $htmlContent .= '<tr><td>' . htmlspecialchars($detail->title) . '</td>';
                            $htmlContent .= '<td>' . htmlspecialchars($detail->stat) . '</td></tr>';
                        }
                    $htmlContent .= '</tbody>
                </table>
            </body>
            </html>';


            $RPContent =  'Hi '.$result->fname.','
            .'<br><br>'.'This is the result of your performance as the resource speaker during '
            .$result->t_title.' at '.$result->t_venue
            .'<br>'.$htmlContent.'<br>'
            ."<br>Thank you, this is from Capacity Building Web Application";

            Notification::route('mail', $result->email)->notify(new NotificationsSendEmailRP($RPContent));

            } // end foreach results

            return redirect()->back()->with('success', 'Rating sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }

    }

    public function TrainingCertificateRequest(){
        $certificateRequests = RequestCertificate::with(['trainingParticipants','training'])
        ->orderBy('created_at', 'desc')
        ->get();
        return Inertia::render('Training/RequestCertificate', 
        [
            'certificateRequests' => $certificateRequests
        ]);
    }

    public function UpdateTrainingCertificateRequest(Request $request){

        $requestCert = RequestCertificate::with(['trainingParticipants','training'])->where('id',$request->id)->first();

        RequestCertificate::find($request->id)->update([
            'is_approve' => $request->status
        ]);

        if($request->status == 1){ //approve
            $project = [
                'greeting' => 'Hi '.$requestCert->trainingParticipants->fname.',',
                'body' => 'This is the certificate of participation on '. $requestCert->training->title,
                'thanks' => 'Thank you this is from Capacity Building Web Application',
                'actionText' => 'Download Certificate',
                'actionURL' => route('public.cert.participant', [
                    'l_name' => $requestCert->trainingParticipants->lname,
                    'f_name' => $requestCert->trainingParticipants->fname,
                    'm_name' => $requestCert->trainingParticipants->mname,
                    'ext_name' => $requestCert->trainingParticipants->ext_name,
                    'training_id' => $requestCert->training_id,
                ]),
            ];
        }else{
            $project = [
                'greeting' => 'Hi '.$requestCert->trainingParticipants->fname.',',
                'body' => 'We are sorry to inform you that your request on the certificate of participation on '. $requestCert->training->title.' has been rejected.',
                'thanks' => 'Thank you this is from Capacity Building Web Application',
                'actionText' => '',
                'actionURL' => '',
            ];
        }

        Notification::route('mail', $requestCert->trainingParticipants->email)->notify(new NotificationsSendEmail($project));

        return Inertia::location(route('training.certificate-request'));
    }

    public function SendEvaluation(Request $request)
    {
        $training_id = $request->training_id;

        // Get all participants for this training, including their first name and email
        $participants = TrainingParticipant::where('training_id', $training_id)->get();

     

        foreach ($participants as $participant) {
            $url = url("/training/{$training_id}/evaluation-response/{$participant->id}/public");
            $project = [
                'greeting' => 'Hi ' . $participant->fname . ',',
                'body' => 'We value your feedback! Please take a moment to complete the evaluation for the training titled "' . $participant->training->title . '". Your insights will help us improve future learning sessions.',
                'thanks' => 'Thank you, this is from Capacity Building Web Application',
                'actionText' => 'Open Evaluation',
                'actionURL' => $url,
            ];

            // Send notification to each participant
            Notification::route('mail', $participant->email)
                ->notify(new \App\Notifications\SendEmailEvaluation($project));
        }

        return response()->json(['message' => 'Evaluation emails sent successfully']);
    }
    
}
