<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use App\Models\User;
use App\Notifications\MfaOtpNotification;

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

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 10 minutes
        $cacheKey = "mfa_setup_otp:{$user->id}";
        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        // Send OTP via email
        if ($user->email) {
            try {
                $userName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: $user->username ?? $user->email;
                Notification::route('mail', $user->email)
                    ->notify(new MfaOtpNotification($otp, $userName, true));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send MFA OTP email: ' . $e->getMessage());
            }
        }

        return Inertia::render('Mfa/Setup', [
            'email' => $user->email,
            'message' => 'A verification code has been sent to your email address. Please check your inbox to complete the 2FA setup.',
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = Auth::user();

        if (!$user && Session::has('2fa_setup:user:id')) {
            $user = User::find(Session::get('2fa_setup:user:id'));
        }

        if (!$user) {
            return redirect('/login');
        }

        // Verify OTP from cache
        $cacheKey = "mfa_setup_otp:{$user->id}";
        $storedOtp = Cache::get($cacheKey);

        if ($storedOtp && $storedOtp === $request->code) {
            // OTP is valid, enable MFA
            $user->mfa_enabled = true;
            $user->save();

            // Clear OTP from cache
            Cache::forget($cacheKey);

            // Clean up and log in if user is a guest
            Session::forget('2fa_setup:user:id');

            if (!Auth::check()) {
                Auth::login($user);
            }

            return redirect('/dashboard')->with('message', '2FA Enabled!');
        }

        return back()->withErrors(['code' => 'Invalid or expired verification code']);
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
    
        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 10 minutes
        $cacheKey = "mfa_otp:{$user->id}";
        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        // Send OTP via email
        if ($user->email) {
            try {
                $userName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: $user->username ?? $user->email;
                Notification::route('mail', $user->email)
                    ->notify(new MfaOtpNotification($otp, $userName, false));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send MFA OTP email: ' . $e->getMessage());
            }
        }
    
        return Inertia::render('Mfa/Prompt', [
            'email' => $user->email,
            'message' => 'A verification code has been sent to your email address. Please check your inbox and enter the 6-digit code.',
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $userId = Session::get('mfa:user:id');
        $user = User::findOrFail($userId);

        // Verify OTP from cache
        $cacheKey = "mfa_otp:{$user->id}";
        $storedOtp = Cache::get($cacheKey);

        if ($storedOtp && $storedOtp === $request->code) {
            // OTP is valid, log in the user
            Auth::login($user);
            
            // Clear OTP from cache
            Cache::forget($cacheKey);
            
            Session::forget('mfa:user:id');
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Invalid or expired verification code']);
    }
}
