<?php

namespace App\Http\Controllers;

use App\Models\Conf\OfficeRepresentative;
use Illuminate\Http\Request;

class OfficeRepresentativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OfficeRepresentative::orderBy('order')->get();
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

        OfficeRepresentative::create($validated);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conf\OfficeRepresentative  $officeRepresentative
     * @return \Illuminate\Http\Response
     */
    public function show(OfficeRepresentative $officeRepresentative)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conf\OfficeRepresentative  $officeRepresentative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $officeRepresentative = OfficeRepresentative::findOrFail($id);
        $request->validate([
            'title' => 'required|string|min:5',
            'order' => 'required|max:20',
            'is_default' => 'required|boolean'
        ]);
        $officeRepresentative->title = $request->title;
        $officeRepresentative->order = $request->order;
        $officeRepresentative->is_default = $request->is_default;
        $officeRepresentative->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conf\OfficeRepresentative  $officeRepresentative
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $officeRepresentative = OfficeRepresentative::findOrFail($id);
        $officeRepresentative->delete();
        return redirect()->back();
    }
}
