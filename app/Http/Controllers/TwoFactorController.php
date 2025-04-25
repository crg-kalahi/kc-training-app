<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Inertia\Inertia;
use App\Models\User;

class TwoFactorController extends Controller
{
    public function setup()
    {
        $user = Auth::user();

        // Fallback to session-stored user ID if logged out
        if (!$user && session()->has('2fa_setup:user:id')) {
            $user = User::find(session('2fa_setup:user:id'));
        }

        // Final guard
        if (!$user) {
            abort(403, 'Unauthorized. No user available for 2FA setup.');
        }

        $google2fa = new Google2FA();

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        // Generate QR code image using BaconQrCode
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return Inertia::render('Auth/TwoFactorSetup', [
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $user->google2fa_secret,
            'email' => $user->email,
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required']);

        $user = Auth::user();

        if (!$user && Session::has('2fa_setup:user:id')) {
            $user = User::find(Session::get('2fa_setup:user:id'));
        }

        if (!$user) {
            return redirect('/login');
        }

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            $user->mfa_enabled = true;
            $user->save();

            // Clean up and log in if user is a guest
            Session::forget('2fa_setup:user:id');

            if (!Auth::check()) {
                Auth::login($user);
            }

            return redirect('/dashboard')->with('message', '2FA Enabled!');
        }

        return back()->withErrors(['code' => 'Invalid token']);
    }

    public function showPrompt()
    {
        if (!Session::has('mfa:user:id')) {
            return redirect('/login');
        }
    
        $user = \App\Models\User::find(Session::get('mfa:user:id'));
    
        if (!$user) {
            return redirect('/login');
        }
    
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
    
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );
    
        // Generate QR code image
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
    
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);
    
        return Inertia::render('Mfa/Prompt', [
            'qrCode' => $qrCodeSvg,
            'secret' => $user->google2fa_secret,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);

        $userId = Session::get('mfa:user:id');
        $user = User::findOrFail($userId);
        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            Auth::login($user);
            Session::forget('mfa:user:id');
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Invalid code']);
    }
}
