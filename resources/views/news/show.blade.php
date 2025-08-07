@extends('layouts.app')
@section('title', $article->title)
@section('content')
<div class="container mx-auto px-4 py-8 text-white">
    <div class="max-w-4xl mx-auto">
        @if(Auth::user()->role == 'user')
            <a href="{{ route('dashboard') }}" class="text-orange-400 hover:text-orange-300 mb-6 inline-block">&larr; Gönüllü Paneline Geri Dön</a>
        @else
             <a href="{{ route('news.index') }}" class="text-orange-400 hover:text-orange-300 mb-6 inline-block">&larr; Tüm Haberlere Geri Dön</a>
        @endif
        
        {{-- DÜZELTME: Resim yolu asset('storage/' . ...) olarak güncellendi --}}
<img src="{{ asset($article->image_url) }}" alt="{{ $article->title }}" class="w-full h-96 object-cover rounded-xl mb-6">        
        <h1 class="text-5xl font-bold mb-4">{{ $article->title }}</h1>
        <p class="text-gray-500 mb-6">Yayınlanma Tarihi: {{ $article->published_at->format('d F Y') }}</p>
        <div class="prose prose-invert lg:prose-xl max-w-none text-gray-300 leading-8">
            {!! nl2br(e($article->content)) !!}
        </div>
    </div>
</div>
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        const container = document.getElementById('message-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>

@endsection