<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();
        $this->validateRecaptcha();
        RateLimiter::clear($this->throttleKey());

        // First, find user locally
        $user = User::where('username', request('username'))->first();

        if ($user) {
            // User found locally, verify password
            if (!Hash::check(request('password'), $user->password)) {
                throw ValidationException::withMessages([
                    'username' => trans('auth.failed'),
                ]);
            }

            // User is authenticated locally
            Auth::login($user);

            // Check if MFA is enabled
            if ($user->mfa_enabled) {
                if (!$user->google2fa_secret) {
                    session(['2fa_setup:user:id' => $user->id]);
                    Auth::logout();

                    throw ValidationException::withMessages([
                        'setup' => 'You must complete 2FA setup.',
                    ]);
                }

                session(['mfa:user:id' => $user->id]);
                Auth::logout();

                throw ValidationException::withMessages([
                    'mfa' => 'MFA required.',
                ]);
            }

            return; // ✅ login successful, continue
        }

        // User not found locally, authenticate with external portal
        $credentials = request()->only('username', 'password');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('CC_TOKEN'),
            'Accept' => 'application/json',
        ])->post(env('CC_URL') . '/api/v1/staff/portal/login', $credentials);

        if ($response->failed()) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

   

        // External authentication success, register user locally
        $userData = $response->json('data');



        $user = User::updateOrCreate([
            'username' => $userData['username'],
        ], [
            'lname' => $userData['last_name'],
            'mname' => $userData['middle_name'],
            'fname' => $userData['first_name'],
            'id_number' => $userData['id_number'],
            'email' => $userData['email'] ?? null,
            'username' => $userData['username'] ?? null,
            'division' => $userData['division'] ?? null,
            'section' => $userData['section'] ?? null,
            'mobile_no' => isset($userData['contact'])
                ? (str_starts_with($userData['contact'], '+63') 
                    ? $userData['contact']
                    : (str_starts_with($userData['contact'], '09')
                        ? preg_replace('/^09/', '+639', $userData['contact'])
                        : $userData['contact']))
                : null,
            'password' => bcrypt($credentials['password']),
            'google2fa_secret' => (new Google2FA())->generateSecretKey(),
            'mfa_enabled' => true,
            'mfa_verified' => false,
        ]);

        if (!$user->hasRole('guest')) {
            $user->assignRole('guest');
        }

        // Log in the newly registered user
        Auth::login($user);

        // Force MFA setup since this is a new user
       // Force MFA setup since this is a new user
        session(['2fa_setup:user:id' => $user->id]);
        Auth::logout();

        // Redirect directly instead of throwing
        return redirect()->route('mfa.setup');
    }

    protected function loginUser(User $user): void
    {
        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }


    private function validateRecaptcha()
    {
        $recaptchaSecret = env('GOOGLE_RECAPTCHA_SECRET');
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $recaptchaSecret,
            'response' => $this->input('recaptcha_token'),
        ]);

        $recaptchaData = $recaptchaResponse->json();
        
        if (!$recaptchaData['success'] || $recaptchaData['score'] < 0.5) {
            throw ValidationException::withMessages([
                'recaptcha' => __('ReCAPTCHA verification failed. Please try again.'),
            ]);
        }
    }

}
