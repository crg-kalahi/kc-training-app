<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function Index(Request $request)
    {
        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'mname' => $user->mname,
                    'ext_name' => $user->ext_name,
                    'id_number' => $user->id_number,
                    'email' => $user->email,
                    'username' => $user->username,
                    'division' => $user->division,
                    'section' => $user->section,
                    'mobile_no' => $user->mobile_no,
                    'created_at' => $user->created_at->toDateTimeString(),
                    'avatar' => $user->avatar,
                    'roles' => $user->roles->pluck('name')->values()->all(),
                ];
            });

        $roles = Role::pluck('name');

        return Inertia::render('UserManagement/Index', [
            'users' => $users,
            'roles' => $roles,
            'canEditUsername' => $request->user()->hasRole('staff-admin'),
        ]);
    }

    public function store(Request $request)
    {
        $isAdmin = $request->user()->hasRole('staff-admin');

        $rules = [
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'mname' => ['nullable', 'string', 'max:255'],
            'ext_name' => ['nullable', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:255', 'unique:users,id_number'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'division' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'mobile_no' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ];

        if ($isAdmin) {
            $rules['username'] = ['nullable', 'string', 'max:255', Rule::unique('users', 'username')];
        }

        $validated = $request->validate($rules);

        $login = strtolower(trim($validated['email']));
        $username = $login;
        if ($isAdmin && ! empty(trim($validated['username'] ?? ''))) {
            $username = strtolower(trim($validated['username']));
        }

        if (User::where('username', $username)->exists()) {
            throw ValidationException::withMessages([
                'username' => 'This login username is already taken.',
            ]);
        }

        $user = User::create([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'mname' => $validated['mname'] ?? null,
            'ext_name' => $validated['ext_name'] ?? null,
            'id_number' => $validated['id_number'],
            'email' => $login,
            'username' => $username,
            'division' => $validated['division'] ?? null,
            'section' => $validated['section'] ?? null,
            'mobile_no' => $validated['mobile_no'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->back();
    }

    public function update(Request $request, User $user)
    {
        $isAdmin = $request->user()->hasRole('staff-admin');

        $rules = [
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'mname' => ['nullable', 'string', 'max:255'],
            'ext_name' => ['nullable', 'string', 'max:255'],
            'id_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'id_number')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'division' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'mobile_no' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ];

        if ($isAdmin) {
            $rules['username'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ];
        }

        $validated = $request->validate($rules);

        $login = strtolower(trim($validated['email']));

        $payload = [
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'mname' => $validated['mname'] ?? null,
            'ext_name' => $validated['ext_name'] ?? null,
            'id_number' => $validated['id_number'],
            'email' => $login,
            'username' => $isAdmin ? strtolower(trim($validated['username'])) : $login,
            'division' => $validated['division'] ?? null,
            'section' => $validated['section'] ?? null,
            'mobile_no' => $validated['mobile_no'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);
        $user->syncRoles($validated['roles']);

        return redirect()->back();
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            throw ValidationException::withMessages([
                'delete' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();

        return redirect()->back();
    }

    public function userManagementRoles(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'uuid', 'exists:users,id'],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $target = User::findOrFail($data['id']);
        $target->syncRoles($data['roles']);

        return redirect()->back();
    }
}
