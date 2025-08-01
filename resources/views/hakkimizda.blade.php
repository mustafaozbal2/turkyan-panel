@extends('layouts.app')

@section('title', 'Hakkımızda')

@push('styles')
{{-- ... (style kodları aynı kalıyor) ... --}}
@endpush

@section('content')
{{-- ARTIK @php bloğuna ihtiyacımız yok, veriler Controller'dan geliyor --}}

<div class="bg-black text-gray-200">

    <div class="hero-section text-center py-28 px-4">
        {{-- ... (bu kısım aynı kalıyor) ... --}}
    </div>

    <div class="container mx-auto px-6 py-24">
        {{-- ... (bu kısım aynı kalıyor) ... --}}
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
                    <img class="w-32 h-32 rounded-full mx-auto ring-4 ring-gray-700" src="{{ $member->image_url }}" alt="{{ $member->name }}">
                    <h3 class="mt-6 text-xl font-semibold text-white">{{ $member->name }}</h3>
                    <p class="text-orange-500">{{ $member->role }}</p>
                    <div class="mt-4 flex justify-center space-x-4">
                        <a href="{{ $member->linkedin_url }}" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin text-2xl"></i></a>
                        <a href="{{ $member->github_url }}" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-github text-2xl"></i></a>
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
                        <i class="fas {{ $value->icon }} text-2xl text-orange-500"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">{{ $value->title }}</h3>
                    <p class="text-gray-400 leading-relaxed">{{ $value->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="bg-gray-900 py-24">
        {{-- ... (bu kısım aynı kalıyor) ... --}}
    </div>
</div>
@endsection