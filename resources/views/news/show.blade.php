@extends('layouts.app')
@section('title', $article->title)
@section('content')
<div class="container mx-auto px-4 py-8 text-white">
    <div class="max-w-4xl mx-auto">
        {{-- DEĞİŞTİ: Link artık Gönüllü Paneli'ne gidiyor --}}
        <a href="{{ route('dashboard') }}" class="text-orange-400 hover:text-orange-300 mb-6 inline-block">&larr; Ana Panele Geri Dön</a>
        
        {{-- DEĞİŞTİ: Resim yolu artık asset() ile güvenli bir şekilde çağrılıyor --}}
        <img src="{{ asset($article->image_url) }}" alt="{{ $article->title }}" class="w-full h-96 object-cover rounded-xl mb-6">
        
        <h1 class="text-5xl font-bold mb-4">{{ $article->title }}</h1>
        <p class="text-gray-500 mb-6">Yayınlanma Tarihi: {{ $article->published_at->format('d F Y') }}</p>
        <div class="prose prose-invert lg:prose-xl max-w-none text-gray-300 leading-8">
            {!! nl2br(e($article->content)) !!} {{-- nl2br ile satır atlamalarını koruyoruz --}}
        </div>
    </div>
</div>
@endsection