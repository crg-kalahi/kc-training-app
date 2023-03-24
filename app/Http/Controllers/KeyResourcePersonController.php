<?php

namespace App\Http\Controllers;

use App\Models\Conf\KeyResourcePerson;
use Illuminate\Http\Request;

class KeyResourcePersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return KeyResourcePerson::orderBy('order')->get();
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

        KeyResourcePerson::create($validated);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conf\KeyResourcePerson  $keyResourcePerson
     * @return \Illuminate\Http\Response
     */
    public function show(KeyResourcePerson $keyResourcePerson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conf\KeyResourcePerson  $keyResourcePerson
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $keyResourcePerson = KeyResourcePerson::findOrFail($id);
        $request->validate([
            'title' => 'required|string|min:5',
            'order' => 'required|max:20',
            'is_default' => 'required|boolean'
        ]);
        $keyResourcePerson->title = $request->title;
        $keyResourcePerson->order = $request->order;
        $keyResourcePerson->is_default = $request->is_default;
        $keyResourcePerson->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conf\KeyResourcePerson  $keyResourcePerson
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keyResourcePerson = KeyResourcePerson::findOrFail($id);
        $keyResourcePerson->delete();
        return redirect()->back();
    }
}
