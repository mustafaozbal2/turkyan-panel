@extends('layouts.app')

@section('title', 'Uçak Kontrol')

@section('content')
<style>
    .ucak-bg {
        background-image: url('/images/ucak-arka.jpg'); /* Buraya kendi resim yolunu koy */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
    }
</style>

<div class="ucak-bg flex items-center justify-center px-4 py-16">
    <div class="bg-black bg-opacity-60 backdrop-blur-md p-10 rounded-xl w-full max-w-4xl">

        @if (session('success'))
            <div class="bg-green-600 text-white text-center font-semibold py-2 rounded mb-4 shadow">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-600 text-white text-center font-semibold py-2 rounded mb-4 shadow">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Başlat Butonu --}}
            <form method="POST" action="{{ route('ucak.baslat') }}">
                @csrf
                <button type="submit" class="w-full h-32 text-2xl bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg transition duration-200">
                    🟢 UÇAĞI BAŞLAT
                </button>
            </form>

            {{-- Durdur Butonu --}}
            <form method="POST" action="{{ route('ucak.durdur') }}">
                @csrf
                <button type="submit" class="w-full h-32 text-2xl bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-lg transition duration-200">
                    🔴 UÇAĞI DURDUR
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
