<?php

namespace App\Http\Controllers;

use App\Models\Conf\Learning;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Learning::orderBy('order')->get();
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

        Learning::create($validated);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conf\Learning  $learning
     * @return \Illuminate\Http\Response
     */
    public function show(Learning $learning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conf\Learning  $learning
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $learning = Learning::findOrFail($id);
        $request->validate([
            'title' => 'required|string|min:5',
            'order' => 'required|max:20',
            'is_default' => 'required|boolean'
        ]);
        $learning->title = $request->title;
        $learning->order = $request->order;
        $learning->is_default = $request->is_default;
        $learning->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conf\Learning  $learning
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $learning = Learning::findOrFail($id);
        $learning->delete();
        return redirect()->back();
    }
}
