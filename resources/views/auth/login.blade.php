@extends('layouts.auth')

@section('title', 'Giriş Yap')

@section('content')
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="text-orange-500 text-4xl font-bold flex items-center justify-center space-x-3">
            <i class="fas fa-fire"></i>
            <span>TÜRKYAN</span>
        </a>
        <p class="text-gray-400 mt-2">Komuta Kontrol Merkezine Hoş Geldiniz</p>
    </div>
    <div class="auth-panel rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Giriş Yap</h2>
        
        {{-- DÜZELTME BURADA: Başarı mesajını göstermek için bu bölüm eklendi --}}
        @if (session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg relative mb-4 text-center" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">E-posta Adresi</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-input mt-1 block w-full rounded-md p-3">
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Şifre</label>
                <input type="password" name="password" id="password" required class="form-input mt-1 block w-full rounded-md p-3">
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-500 bg-gray-700 text-orange-600 focus:ring-orange-500">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-300">Beni Hatırla</label>
                </div>
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-orange-400 hover:text-orange-300">Şifrenizi mi unuttunuz?</a>
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded-md transition-all duration-300 transform hover:scale-105">Giriş Yap</button>
            </div>
        </form>
        <p class="text-center text-gray-400 text-sm mt-6">
            Hesabınız yok mu? <a href="{{ route('register') }}" class="font-medium text-orange-400 hover:text-orange-300">Kayıt Ol</a>
        </p>
    </div>
</div>
@endsection