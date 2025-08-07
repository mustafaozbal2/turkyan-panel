@extends('layouts.app')
@section('title', 'Haberler ve Duyurular')
@section('content')
<div class="container mx-auto px-4 py-8 text-white">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold">Haberler & Duyurular</h1>
        @if(Auth::user()->role == 'user')
        <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">
            &larr; Gönüllü Paneline Geri Dön
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-md relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($articles as $article)
        <div class="bg-gray-800/50 dashboard-panel rounded-xl overflow-hidden flex flex-col">
            {{-- DÜZELTME: Resim yolu asset('storage/' . ...) olarak güncellendi --}}
<img src="{{ asset($article->image_url) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">            <div class="p-6 flex flex-col flex-grow">
                <h2 class="text-xl font-bold text-white mb-2">{{ $article->title }}</h2>
                <p class="text-gray-400 text-sm mb-4 flex-grow">{{ Str::limit($article->content, 100) }}</p>
                <div class="mt-auto">
                    <a href="{{ route('news.show', $article->slug) }}" class="bg-orange-600 hover:bg-orange-500 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">Devamını Oku</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection