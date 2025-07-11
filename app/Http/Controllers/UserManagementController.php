<?php

namespace App\Http\Controllers;

use App\Models\Conf\KeyResourcePerson;
use App\Models\Conf\KeyTraining;
use App\Models\Conf\Learning;
use App\Models\Conf\OfficeRepresentative;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function Index(){
        // $users = User::all();
        $users = User::with('roles')  
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
              'roles' => $user->roles->pluck('name')
            ];
        });

        $roles = Role::pluck('name');
        
        return Inertia::render('UserManagement/Index', ['users'=>$users,'roles'=>$roles]);
    }

    public function userManagementRoles(Request $request){
        $data = $request->all();

        $user = User::find($data['id']);

        // Sync provided roles
        $user->syncRoles($data['roles']);

        return redirect()->back();
    }
}
