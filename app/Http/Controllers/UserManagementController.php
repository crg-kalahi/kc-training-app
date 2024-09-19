<?php

namespace App\Http\Controllers;

use App\Models\Conf\KeyResourcePerson;
use App\Models\Conf\KeyTraining;
use App\Models\Conf\Learning;
use App\Models\Conf\OfficeRepresentative;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function Index(){
        // $users = User::all();
        $users = User::with('permissions') // Ensure 'permissions' is a valid relationship
        ->orderBy('created_at', 'desc') // Use 'asc' for ascending order
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'fname' => $user->fname, 
                'lname' => $user->lname, 
                'email' => $user->email,
                'created_at' => $user->created_at->toDateTimeString(), // Format as needed
                'avatar' => $user->avatar, // Use accessor for avatar
                'permissions' => $user->getAllPermissions()->pluck('name') // Retrieve permissions and get their names
            ];
        });

        $permissions = Permission::pluck('name');
        
        return Inertia::render('UserManagement/Index', ['users'=>$users,'permissions'=>$permissions]);
    }

    public function userManagementPermissions(Request $request){
        $data = $request->all();
        $user = User::find($data['id']);

        // Ensure permissions are properly updated
        $user->syncPermissions($data['permissions']); // Sync provided permissions

        // Redirect back to the index page to reload the updated data
        // return redirect()->route('user-management');
        // return response()->json(['msg' => 'success']);
        return redirect()->back();
    }
}
