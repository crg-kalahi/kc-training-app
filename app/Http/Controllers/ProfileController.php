<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Me/Profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateMfa(Request $request)
    {
        $request->validate([
            'mfa_enabled' => 'required|boolean',
        ]);

        $user = Auth::user();
        $user->mfa_enabled = $request->mfa_enabled;
        $user->save();

        return back()->with('success', 'MFA status updated.');
    }
}
