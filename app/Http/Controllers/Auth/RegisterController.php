<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request; // <-- Bu satırı ekledik

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    */

    use RegistersUsers;

    /**
     * DÜZELTME: Kayıt sonrası yönlendirme hedefini kaldırdık çünkü
     * aşağıdaki fonksiyonla kendi yönlendirmemizi yapacağız.
     * protected $redirectTo = '/home'; // <-- Bu satır silindi veya yorum yapıldı.
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // Not: Yeni kullanıcıya varsayılan bir rol atamak istersen,
            // buraya 'role' => 'user' gibi bir satır ekleyebilirsin.
        ]);
    }

    /**
     * DÜZELTME: Bu fonksiyonu ekleyerek kayıt sonrası işlemi eziyoruz.
     * Kullanıcı oluşturulduktan sonra çalışır.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Kullanıcının otomatik olarak giriş yapmasını engellemek için oturumu sonlandırıyoruz.
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Şimdi kullanıcıyı "başarı" mesajıyla birlikte login sayfasına yönlendiriyoruz.
        return redirect('/login')->with('success', 'Kaydınız başarıyla oluşturuldu! Şimdi giriş yapabilirsiniz.');
    }
}