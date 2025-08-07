@extends('layouts.auth')

@section('title', 'Şifre Sıfırlama')

@section('content')
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="text-orange-500 text-4xl font-bold flex items-center justify-center space-x-3">
            <i class="fas fa-fire"></i>
            <span>TÜRKYAN</span>
        </a>
    </div>
    <div class="auth-panel rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Şifremi Unuttum</h2>
        <p class="text-center text-gray-400 text-sm mb-6">Hesabınıza kayıtlı e-posta adresini girerek şifre sıfırlama talebi oluşturabilirsiniz.</p>
        
        @if (session('status'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-md relative mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.request') }}" method="POST">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">E-posta Adresi</label>
                <input id="email" type="email" name="email" required class="form-input mt-1 block w-full rounded-md p-3" placeholder="ornek@mail.com">
                @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
            <div class="pt-6">
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded-md transition-all duration-300">
                    Sıfırlama Linki Gönder
                </button>
            </div>
        </form>
         <p class="text-center text-gray-400 text-sm mt-6">
            <a href="{{ route('login') }}" class="font-medium text-orange-400 hover:text-orange-300">Giriş Ekranına Geri Dön</a>
        </p>
    </div>
</div>
@endsection