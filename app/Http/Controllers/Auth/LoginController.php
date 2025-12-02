<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('recuerdame'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')->with('success', 'Bienvenido al Sistema de Laboratorio');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(): RedirectResponse
    {
        $email = request()->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($email);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Enlace de recuperación enviado a su correo electrónico')
            : back()->with('error', 'No se pudo enviar el enlace de recuperación');
    }

    public function showResetForm($token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(): RedirectResponse
    {
        $credentials = request()->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $status = Password::reset($credentials, function ($user, $password) {
            $user->forceFill(['password' => bcrypt($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect('/login')->with('success', 'Contraseña restablecida correctamente')
            : back()->with('error', 'El enlace de recuperación es inválido o ha expirado');
    }
}
