<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo valido.',
            'password.required' => 'La contrasena es obligatoria.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Usuario no encontrado.'])
                ->withInput($request->only('email'));
        }

        if (!$user->activo) {
            return back()
                ->withErrors(['email' => 'Tu usuario esta inactivo. Comunicate con el administrador.'])
                ->withInput($request->only('email'));
        }

        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'activo' => true,
        ])) {
            return back()
                ->withErrors(['email' => 'Correo o contrasena incorrectos.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return $this->redirectToDashboard(Auth::user(), $request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectToDashboard(User $user, ?Request $request = null): RedirectResponse
    {
        $route = $user->dashboardRouteName();

        if (!$route || !Route::has($route)) {
            Auth::logout();

            if ($request) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'No existe un dashboard configurado para este rol.']);
        }

        return redirect()->intended(route($route));
    }
}
