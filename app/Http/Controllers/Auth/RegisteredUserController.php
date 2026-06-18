<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $siteKey = config('services.recaptcha.site_key');
        if ($siteKey !== '') {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $request->input('recaptcha_token', ''),
                'remoteip' => $request->ip(),
            ]);

            $score = $response->json('score', 0);
            if (! $response->json('success') || $score < config('services.recaptcha.threshold')) {
                Log::warning('reCAPTCHA register failed', ['score' => $score, 'ip' => $request->ip()]);
                throw ValidationException::withMessages([
                    'email' => 'Xác minh bảo mật thất bại. Vui lòng thử lại.',
                ]);
            }
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => User::ROLE_CUSTOMER,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('account.orders', absolute: false));
    }
}
