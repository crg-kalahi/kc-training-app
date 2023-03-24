<?php

namespace App\Http\Controllers;

use App\Models\Conf\KeyTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KeyTrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return KeyTraining::orderBy('order')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5',
            'order' => 'required|max:20',
            'is_default' => 'required|boolean',
        ]);

        KeyTraining::create($validated);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conf\KeyTraining  $keyTraining
     * @return \Illuminate\Http\Response
     */
    public function show(KeyTraining $keyTraining)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conf\KeyTraining  $keyTraining
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $keyTraining = KeyTraining::findOrFail($id);
        $request->validate([
            'title' => 'required|string|min:5',
            'order' => 'required|max:20',
            'is_default' => 'required|boolean'
        ]);
        $keyTraining->title = $request->title;
        $keyTraining->order = $request->order;
        $keyTraining->is_default = $request->is_default;
        $keyTraining->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conf\KeyTraining  $keyTraining
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keyTraining = KeyTraining::findOrFail($id);
        $keyTraining->delete();
        return redirect()->back();
    }
}
