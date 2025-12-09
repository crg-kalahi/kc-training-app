<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use App\Models\User;
use App\Notifications\MfaQrCodeNotification;

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

        // Generate QR code as PNG image for PDF
        $qrCodeBase64 = null;
        
        try {
            // Try to use Imagick for PNG generation (better quality)
            if (extension_loaded('imagick') && class_exists('Imagick')) {
                try {
                    $renderer = new ImageRenderer(
                        new RendererStyle(300),
                        new ImagickImageBackEnd()
                    );
                    $writer = new Writer($renderer);
                    $qrCodePng = $writer->writeString($qrCodeUrl);
                    $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);
                } catch (\Exception $e) {
                    \Log::warning('ImagickImageBackEnd failed, trying SVG conversion: ' . $e->getMessage());
                }
            }
            
            // If PNG generation failed or Imagick not available, generate SVG and convert
            if (!$qrCodeBase64) {
                $renderer = new ImageRenderer(
                    new RendererStyle(300),
                    new SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                $qrCodeSvg = $writer->writeString($qrCodeUrl);
                
                // Try to convert SVG to PNG using Imagick
                if (extension_loaded('imagick') && class_exists('Imagick')) {
                    try {
                        $im = new \Imagick();
                        $im->readImageBlob($qrCodeSvg);
                        $im->setImageFormat('png');
                        $im->setImageBackgroundColor(new \ImagickPixel('white'));
                        $im = $im->mergeImageLayers(\Imagick::LAYER_METHOD_FLATTEN);
                        $qrCodePng = $im->getImageBlob();
                        $im->clear();
                        $im->destroy();
                        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);
                    } catch (\Exception $e) {
                        \Log::warning('SVG to PNG conversion failed: ' . $e->getMessage());
                        // Fallback to SVG base64
                        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
                    }
                } else {
                    // No Imagick available, use SVG base64
                    $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code image: ' . $e->getMessage());
            // Final fallback: Generate SVG
            $renderer = new ImageRenderer(
                new RendererStyle(300),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        }

        // Generate PDF with QR code
        $pdfData = null;
        if ($qrCodeBase64) {
            try {
                $userName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: $user->username ?? $user->email;
                $pdf = Pdf::loadView('pdf.mfa_qr_code', [
                    'qrCodeBase64' => $qrCodeBase64,
                    'secret' => $user->google2fa_secret,
                    'userName' => $userName,
                    'isSetup' => true,
                ])->setPaper('a4', 'portrait');
                
                $pdfData = $pdf->output();
            } catch (\Exception $e) {
                \Log::error('Failed to generate PDF: ' . $e->getMessage());
            }
        }

        // Send QR code PDF via email
        if ($user->email && $pdfData) {
            try {
                $userName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: $user->username ?? $user->email;
                Notification::route('mail', $user->email)
                    ->notify(new MfaQrCodeNotification(
                        $pdfData,
                        $user->google2fa_secret,
                        $userName,
                        true
                    ));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send MFA QR code email: ' . $e->getMessage());
            }
        }

        return Inertia::render('Mfa/Setup', [
            'email' => $user->email,
            'message' => 'A QR code has been sent to your email address. Please check your inbox to complete the 2FA setup.',
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
    
        // Generate QR code as PNG image for PDF
        $qrCodeBase64 = null;
        
        try {
            // Try to use Imagick for PNG generation (better quality)
            if (extension_loaded('imagick') && class_exists('Imagick')) {
                try {
                    $renderer = new ImageRenderer(
                        new RendererStyle(300),
                        new ImagickImageBackEnd()
                    );
                    $writer = new Writer($renderer);
                    $qrCodePng = $writer->writeString($qrCodeUrl);
                    $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);
                } catch (\Exception $e) {
                    \Log::warning('ImagickImageBackEnd failed, trying SVG conversion: ' . $e->getMessage());
                }
            }
            
            // If PNG generation failed or Imagick not available, generate SVG and convert
            if (!$qrCodeBase64) {
                $renderer = new ImageRenderer(
                    new RendererStyle(300),
                    new SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                $qrCodeSvg = $writer->writeString($qrCodeUrl);
                
                // Try to convert SVG to PNG using Imagick
                if (extension_loaded('imagick') && class_exists('Imagick')) {
                    try {
                        $im = new \Imagick();
                        $im->readImageBlob($qrCodeSvg);
                        $im->setImageFormat('png');
                        $im->setImageBackgroundColor(new \ImagickPixel('white'));
                        $im = $im->mergeImageLayers(\Imagick::LAYER_METHOD_FLATTEN);
                        $qrCodePng = $im->getImageBlob();
                        $im->clear();
                        $im->destroy();
                        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);
                    } catch (\Exception $e) {
                        \Log::warning('SVG to PNG conversion failed: ' . $e->getMessage());
                        // Fallback to SVG base64
                        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
                    }
                } else {
                    // No Imagick available, use SVG base64
                    $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code image: ' . $e->getMessage());
            // Final fallback: Generate SVG
            $renderer = new ImageRenderer(
                new RendererStyle(300),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        }
    
        // Generate PDF with QR code
        $pdfData = null;
        if ($qrCodeBase64) {
            try {
                $userName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: $user->username ?? $user->email;
                $pdf = Pdf::loadView('pdf.mfa_qr_code', [
                    'qrCodeBase64' => $qrCodeBase64,
                    'secret' => $user->google2fa_secret,
                    'userName' => $userName,
                    'isSetup' => false,
                ])->setPaper('a4', 'portrait');
                
                $pdfData = $pdf->output();
            } catch (\Exception $e) {
                \Log::error('Failed to generate PDF: ' . $e->getMessage());
            }
        }
    
        // Send QR code PDF via email
        if ($user->email && $pdfData) {
            try {
                $userName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: $user->username ?? $user->email;
                Notification::route('mail', $user->email)
                    ->notify(new MfaQrCodeNotification(
                        $pdfData,
                        $user->google2fa_secret,
                        $userName,
                        false
                    ));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send MFA QR code email: ' . $e->getMessage());
            }
        }
    
        return Inertia::render('Mfa/Prompt', [
            'email' => $user->email,
            'message' => 'A verification code has been sent to your email address. Please check your inbox and enter the 6-digit code from your authenticator app.',
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
