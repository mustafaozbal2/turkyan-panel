<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Sadece misafirlerin (giriş yapmamış) bu controller'a erişebilmesini sağlar.
     * (logout fonksiyonu hariç)
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Giriş formunu gösterir.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Giriş denemesini işler ve role göre yönlendirir.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember-me'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // ROL KONTROLÜ VE YÖNLENDİRME
            switch ($user->role) {
                case 'admin':
                case 'itfaiye':
                    return redirect()->intended('/index');
                case 'bakanlik':
                    return redirect()->intended('/bakanlik');
                default: // 'user' rolü ve diğerleri
                    return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Girilen bilgiler kayıtlarımızla eşleşmiyor.',
        ])->onlyInput('email');
    }

    /**
     * Kullanıcının oturumunu sonlandırır.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}