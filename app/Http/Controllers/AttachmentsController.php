<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Attachments;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentsController extends Controller
{
    public function index(){
        $results = Attachments::all();


        return $results;
    }

    public function storeUpdate(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            // 'file' => $request->id ? 'nullable|file' : 'required|file',  // Allow null file on update, but require file for create
        ]);


        if($request->type === null){
            $request->type = ' ';
        }

        
        $url = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            $url = asset('storage/' . $path); // Generate the URL for the file
        }
        
        
    
        if ($request->id) {
            // Handle update logic
            $attachment = Attachments::findOrFail($request->id);
            $attachment->name = strtoupper($request->name);
            $attachment->description = strtoupper($request->description);
            $attachment->type = $request->type;
            if ($url) {
                $attachment->file_path = $url;  // Update file path only if a file was uploaded
            }
            $attachment->save();
    
            return redirect()->back()->with('success', 'Attachment updated successfully!');
        } else {
            // Handle create logic
            Attachments::create([
                'training_id' => $request->training_id,
                'name' => strtoupper($request->name),
                'description' => strtoupper($request->description),
                'file_path' => $url,
                'type' => $request->type,
            ]);
    
            return redirect()->back()->with('success', 'Attachment created successfully!');
        }
    }

    public function removeAttachment(Request $request){
        $request->validate([
            'id' => 'required|exists:attachments,id',
        ]);

        $attachment = Attachments::findOrFail($request->id);
        $attachment->delete();

        return redirect()->back()->with('success', 'Attachment removed successfully!');
    }
}
