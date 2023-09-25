<?php

namespace App\Http\Controllers;

use App\Models\TrainingParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainingParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = DB::table('participant_lists_view')->where('full_name', 'LIKE', '%'.$request->q.'%');
        if(!$request->q){
            $items = $items->limit(7);
        }
        return $items->get();
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
            'training_id' => 'required|string',
            'lname' => 'required|string|max:50',
            'fname' => 'required|string|max:50',
            'is_internal' => 'required|boolean',
            'is_female' => 'required|boolean',
        ]);

        TrainingParticipant::create([
            'training_id' => $request->training_id,
            'lname' => strtolower($request->lname),
            'mname' => strtolower($request->mname),
            'fname' => strtolower($request->fname),
            'ext_name' => strtolower($request->ext_name),
            'is_internal' => $request->is_internal,
            'email' => $request->email,
            'position' => $request->position,
            'is_female' => $request->is_female,
            'pre_test' => $request->pre_test,
            'post_test' => $request->post_test,
            'municipality' => $request->municipality,
            'brgy' => $request->brgy,
            'encoded_by' => Auth::id()
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'training_id' => 'required|string',
            'lname' => 'required|string|max:50',
            'fname' => 'required|string|max:50',
            'is_internal' => 'required|boolean',
            'is_female' => 'required|boolean',
        ]);

        $p = TrainingParticipant::findOrFail($id);

        $p->training_id = $request->training_id;
        $p->lname = strtolower($request->lname);
        $p->mname = strtolower($request->mname);
        $p->fname = strtolower($request->fname);
        $p->ext_name = strtolower($request->ext_name);
        $p->is_internal = $request->is_internal;
        $p->email = $request->email;
        $p->position = $request->position;
        $p->is_female = $request->is_female;
        $p->pre_test = $request->pre_test;
        $p->post_test = $request->post_test;
        $p->municipality = $request->municipality;
        $p->brgy = $request->brgy;

        $p->save();
        return response()->json(['msg' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $p = TrainingParticipant::findOrFail($id);
        $p->delete();
        return redirect()->back();
    }
}
