<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
//    return view('auth.login');
        return view('auth.register', [
            'styles' => ['login.css']
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and account is active
        if (!$user || !$user->is_active) {
            return back()->withErrors([
                'email' => 'Please verify your email address before logging in.',
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
//            return redirect('/dashboard');
            return redirect()->route('user.profile');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register', [
            'styles' => ['login.css']
        ]);
    }
    public function messageSent()
    {
        return view('auth.message-sent', [
            'styles' => ['login.css']
        ]);
    }

    public function forgotPasswordForm()
    {
        return view('auth.forgot-password', [
            'styles' => ['login.css']
        ]);
    }

    public function sendPasswordResetEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Генерируем токен сброса пароля
        $token = Str::random(64);

        // Сохраняем токен в базе
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Отправляем email
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        Mail::send('emails.reset-password', [
            'user' => $user,
            'resetUrl' => $resetUrl
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('🔐 Сброс пароля для вашего аккаунта');
        });

//        return back()->with('success', 'Ссылка для сброса пароля отправлена на вашу почту.');
        return redirect()->route('password.message-sent');
    }

    public function resetPasswordForm(Request $request, $token)
    {
        $email = $request->email;
        return view('auth.reset-password', ['email' => $email, 'token' => $token]);
    }

    public function resetPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $resetData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetData || !Hash::check($request->token, $resetData->token)) {
            return back()->withErrors(['email' => 'Неверный токен сброса пароля.']);
        }

        // Обновляем пароль
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Удаляем использованный токен
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Пароль успешно изменен!');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $email = $request->email;
        $domain = substr(strrchr($email, "@"), 1);

        $userData = [
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'is_active' => false,
            'email_verification_token' => Str::random(32),
        ];

        // Auto-activate @clm.org emails, others require verification
        if ($domain === 'clm.org') {
            $userData['is_active'] = true;
            $userData['email_verified_at'] = now();
            $userData['email_verification_token'] = null;
        }


        $user = User::create($userData);
//    dd($user);
        if ($domain !== 'clm.org') {
            $verificationUrl = route('verify.email', ['token' => $user->email_verification_token]);
            Mail::send('emails.welcome', ['user' => $user, 'verificationUrl' => $verificationUrl], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject("🎩 Добро пожаловать в семью, {$user->name}! Подтверди, что ты с нами...");
            });

            return redirect('/login')->with('success',
                'Registration successful. Please check your email to verify your account.');
        }

        return redirect('/login')->with('success', 'Registration successful. You can now login.');
    }

    public function redirectToGoogle()
    {

        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {

        $googleUser = Socialite::driver('google')->user();

        // Find or create user
        $user = $this->findOrCreateUser($googleUser);

        // Log the user in
        Auth::login($user, true);

        return redirect(route('user.profile'))->with('success', 'Logged in successfully!');


    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->scopes(['email', 'public_profile']) // Request additional permissions
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {

        $facebookUser = Socialite::driver('facebook')->user();

        // Find or create user
        $user = $this->findOrCreateFacebookUser($facebookUser);

        // Log the user in
        Auth::login($user, true);

        return redirect(route('user.profile'))->with('success', 'Logged with Facebook successfully!');


    }

    protected function findOrCreateUser($googleUser)
    {
        // Check if user already exists
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Update Google ID if missing
            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
            return $user;
        }

        // Create new user
        return User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password' => Hash::make(Str::random(24)), // Random password
            'email_verified_at' => now(), // Google emails are verified
            'is_active' => true,
        ]);
    }

    protected function findOrCreateFacebookUser($facebookUser)
    {
        $user = User::where('facebook_id', $facebookUser->getId())
            ->orWhere('email', $facebookUser->getEmail())
            ->first();

        if ($user) {
            // Update Facebook ID if missing
            if (empty($user->facebook_id)) {
                $user->update(['facebook_id' => $facebookUser->getId()]);
            }
            return $user;
        }

        return User::create([
            'name' => $facebookUser->getName(),
            'email' => $facebookUser->getEmail(),
            'facebook_id' => $facebookUser->getId(),
            'avatar' => $facebookUser->getAvatar(),
            'password' => Hash::make(Str::random(24)),
            'email_verified_at' => now(), // Facebook emails are verified
            'is_active' => true,
        ]);
    }

    public function dashboard()
    {
        return view('dashboard', [
            'styles' => ['dashboard.css']

        ]);
    }

    public function showLogout()
    {
        return view('auth.logout');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
