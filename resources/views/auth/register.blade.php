@extends('layouts.auth')

@section('title', 'Kayıt Ol')

@section('content')
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="text-orange-500 text-4xl font-bold flex items-center justify-center space-x-3">
            <i class="fas fa-fire"></i>
            <span>TÜRKYAN</span>
        </a>
        <p class="text-gray-400 mt-2">Afet Yönetimine Destek Olmak İçin Kaydolun</p>
    </div>

    <div class="auth-panel rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Yeni Gönüllü Kaydı</h2>
        
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-300">Ad Soyad</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-input mt-1 block w-full rounded-md p-3">
                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

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
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Şifre Tekrar</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="form-input mt-1 block w-full rounded-md p-3">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded-md transition-all duration-300 transform hover:scale-105">
                    Hesap Oluştur
                </button>
            </div>
        </form>

        <p class="text-center text-gray-400 text-sm mt-6">
            Zaten bir hesabınız var mı?
            <a href="{{ route('login') }}" class="font-medium text-orange-400 hover:text-orange-300">Giriş Yap</a>
        </p>
    </div>
</div>
@endsection