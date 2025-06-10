<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Tidak dipakai karena kita override redirect di authenticated()
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'nik';  // Gunakan 'nik' sebagai username
    }

    protected function credentials(Request $request)
    {
        return [
            'nik' => $request->input('nik'),
            'password' => $request->input('password'),
        ];
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        \Log::info('Attempt login with credentials:', $credentials);
        return $this->guard()->attempt($credentials, $request->filled('remember'));
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => ['NIK atau password salah.'],
        ]);
    }

    // Tambahkan ini untuk redirect sesuai role setelah login
    protected function authenticated(Request $request, $user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.home');
            case 'ketua':
                return redirect()->route('ketua.home');
            case 'user':
            default:
                return redirect()->route('user.home');
        }
    }

    // Override logout supaya bisa POST
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
