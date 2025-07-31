@extends('layouts.app')

@section('title', 'Hakkımızda')

@push('styles')
{{-- Yeni, modern bir yazı tipi ekliyoruz --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Proje geneline yeni yazı tipini uyguluyoruz */
    body {
        font-family: 'Poppins', sans-serif;
    }

    .hero-section {
        background-image: linear-gradient(rgba(18, 18, 18, 0.85), rgba(18, 18, 18, 1)), url('https://images.unsplash.com/photo-1594980595429-99a38f18e2a3?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
    }

    /* Kartlara ve logolara yumuşak geçiş efekti ekliyoruz */
    .team-card, .tech-logo, .value-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .team-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
    }
    
    .tech-logo {
        filter: grayscale(80%) opacity(70%);
    }

    .tech-logo:hover {
        filter: grayscale(0%) opacity(100%);
        transform: scale(1.1);
    }

    /* Ana başlığa istediğiniz "kabartma/parlama" efektini ekliyoruz */
    .hero-title-glow {
        text-shadow: 0 0 15px rgba(249, 115, 22, 0.5), 0 0 5px rgba(249, 115, 22, 0.4);
    }
</style>
@endpush

@section('content')

@php
// Bu veri yapısı aynı kalıyor
$team_members = [
    ['name' => 'Mustafa Özbal', 'role' => 'Proje Lideri & Geliştirici', 'img' => 'https://placehold.co/400x400/27272a/FFFFFF?text=MÖ', 'social' => ['linkedin' => '#', 'github' => '#']],
    ['name' => 'Reyhan Er', 'role' => 'Arayüz Tasarımı & Frontend', 'img' => 'https://placehold.co/400x400/27272a/FFFFFF?text=RE', 'social' => ['linkedin' => '#', 'github' => '#']],
    ['name' => 'Bayram Cellat', 'role' => 'Sistem Analisti & Veri Uzmanı', 'img' => 'https://placehold.co/400x400/27272a/FFFFFF?text=BC', 'social' => ['linkedin' => '#', 'github' => '#']],
];

$values = [
    ['icon' => 'fa-bolt', 'title' => 'Hız', 'desc' => 'Tehlike anında saniyelerin önemi vardır. Sistemimiz, en hızlı tespiti ve bildirimi sağlamak üzere tasarlanmıştır.'],
    ['icon' => 'fa-shield-alt', 'title' => 'Güvenilirlik', 'desc' => '7/24 kesintisiz çalışan, kararlı ve güvenilir altyapımızla her an göreve hazırız.'],
    ['icon' => 'fa-lightbulb', 'title' => 'İnovasyon', 'desc' => 'En son yapay zeka ve sensör teknolojilerini kullanarak sürekli daha iyisi için çalışıyoruz.'],
];
@endphp

{{-- ANA YAPI VE İSKELET AYNI KALDI, SADECE TASARIMSAL DOKUNUŞLAR YAPILDI --}}
<div class="bg-black text-gray-200">

    <div class="hero-section text-center py-28 px-4">
        <h1 class="text-6xl font-bold tracking-tight text-white">Biz <span class="text-orange-500 hero-title-glow">TÜRKYAN</span>'ız</h1>
        <p class="max-w-4xl mx-auto mt-6 text-lg text-gray-300 leading-8">
            Teknolojiyi doğayı korumak için bir kalkan olarak kullanarak, ülkemizin yeşil mirasını yarınlara taşımayı misyon edindik.
        </p>
    </div>

    <div class="container mx-auto px-6 py-24">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="bg-gray-900/50 p-8 rounded-xl border border-gray-800 backdrop-blur-sm">
                <h2 class="text-3xl font-bold mb-4 text-white">Misyonumuz</h2>
                <p class="text-gray-400 leading-relaxed">
                    Türkiye'nin ormanlarını ve doğal yaşam alanlarını, gelişmiş uydu teknolojisi ve yapay zeka destekli analiz sistemleri ile proaktif bir şekilde izleyerek, yangın risklerini oluşmadan önce tespit etmek ve müdahale ekiplerini en etkin şekilde yönlendirerek zararı en aza indirmektir.
                </p>
            </div>
            <div class="bg-gray-900/50 p-8 rounded-xl border border-orange-500/30 backdrop-blur-sm">
                <h2 class="text-3xl font-bold mb-4 text-orange-500">Vizyonumuz</h2>
                <p class="text-gray-400 leading-relaxed">
                    Yangın yönetiminde ulusal ve uluslararası alanda referans gösterilen, teknolojik inovasyonları ve operasyonel mükemmelliği ile standartları belirleyen lider bir kurum olmaktır.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-gray-900 py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white">Projenin Arkasındaki Ekip</h2>
                <p class="text-gray-400 mt-3 max-w-2xl mx-auto">Bu projeyi hayata geçiren tutkulu ve adanmış insanlar.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach ($team_members as $member)
                <div class="team-card text-center bg-gray-800/50 p-8 rounded-xl border border-gray-700">
                    <img class="w-32 h-32 rounded-full mx-auto ring-4 ring-gray-700" src="{{ $member['img'] }}" alt="{{ $member['name'] }}">
                    <h3 class="mt-6 text-xl font-semibold text-white">{{ $member['name'] }}</h3>
                    <p class="text-orange-500">{{ $member['role'] }}</p>
                    <div class="mt-4 flex justify-center space-x-4">
                        <a href="{{ $member['social']['linkedin'] }}" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin text-2xl"></i></a>
                        <a href="{{ $member['social']['github'] }}" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-github text-2xl"></i></a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="py-24">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-16 text-white">Temel Değerlerimiz</h2>
            <div class="grid md:grid-cols-3 gap-10">
                @foreach ($values as $value)
                <div class="value-card bg-gray-900/50 p-8 rounded-xl border border-gray-800 hover:border-orange-500/50 hover:bg-gray-800/50">
                    <div class="w-16 h-16 mx-auto flex items-center justify-center bg-gray-800 rounded-full mb-5 border-2 border-orange-500">
                        <i class="fas {{ $value['icon'] }} text-2xl text-orange-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">{{ $value['title'] }}</h3>
                    <p class="text-gray-400 leading-relaxed">{{ $value['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="bg-gray-900 py-24">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-16 text-white">Teknoloji Altyapımız</h2>
            <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-10">
                <div class="tech-logo" title="Laravel"><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/laravel/laravel-plain.svg" alt="Laravel" class="h-16"></div>
                <div class="tech-logo" title="Python"><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/python/python-original.svg" alt="Python" class="h-16"></div>
                <div class="tech-logo" title="JavaScript"><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/javascript/javascript-original.svg" alt="JavaScript" class="h-16"></div>
                <div class="tech-logo" title="Tailwind CSS"><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/tailwindcss/tailwindcss-plain.svg" alt="Tailwind CSS" class="h-16"></div>
                <div class="tech-logo" title="Leaflet"><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/leaflet/leaflet-original.svg" alt="Leaflet" class="h-16"></div>
                <div class="tech-logo" title="PostgreSQL"><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/postgresql/postgresql-original.svg" alt="PostgreSQL" class="h-16"></div>
            </div>
        </div>
    </div>
</div>
@endsection