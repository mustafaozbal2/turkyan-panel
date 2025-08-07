@extends('layouts.auth')

@section('title', 'Yeni Şifre Belirle')

@section('content')
<div class="w-full max-w-md">
     <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="text-orange-500 text-4xl font-bold flex items-center justify-center space-x-3">
            <i class="fas fa-fire"></i>
            <span>TÜRKYAN</span>
        </a>
    </div>
    <div class="auth-panel rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-white text-center mb-6">Yeni Şifre Belirle</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">E-posta Adresi</label>
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required class="form-input mt-1 block w-full rounded-md p-3">
                    @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Yeni Şifre</label>
                    <input id="password" type="password" name="password" required class="form-input mt-1 block w-full rounded-md p-3">
                    @error('password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password-confirm" class="block text-sm font-medium text-gray-300">Yeni Şifre Tekrar</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required class="form-input mt-1 block w-full rounded-md p-3">
                </div>
            </div>
            <div class="pt-6">
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded-md">Şifreyi Değiştir</button>
            </div>
        </form>
    </div>
</div>
@endsection