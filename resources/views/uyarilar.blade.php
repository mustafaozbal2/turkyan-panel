@extends('layouts.app')

@section('title', 'Sistem Uyarıları ve Olay Akışı')

@push('styles')
    {{-- ... (style kodları aynı kalıyor) ... --}}
@endpush

@section('content')
@php
// Bu @php bloğu artık sadece stil sınıflarını tutuyor, sahte veriyi sildik.
$severity_classes = [
    'Kritik' => ['border' => 'border-red-500', 'bg' => 'bg-red-900/20', 'icon_bg' => 'bg-red-500', 'glow' => 'glow-kritik'],
    'Yüksek' => ['border' => 'border-orange-500', 'bg' => 'bg-orange-900/20', 'icon_bg' => 'bg-orange-500', 'glow' => 'glow-yuksek'],
    'Orta' => ['border' => 'border-yellow-500', 'bg' => 'bg-yellow-900/20', 'icon_bg' => 'bg-yellow-500', 'glow' => ''],
    'Bilgi' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-900/20', 'icon_bg' => 'bg-blue-500', 'glow' => ''],
];
@endphp

<div class="container mx-auto px-4 py-8 text-white">
    {{-- ... (sayfanın üst kısmı aynı kalıyor) ... --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="relative timeline-container">
                {{-- DÖNGÜ ARTIK CONTROLLER'DAN GELEN VERİYİ KULLANIYOR --}}
                @forelse ($alerts as $alert)
                    @php $classes = $severity_classes[$alert->severity] ?? $severity_classes['Bilgi']; @endphp
                    <div class="timeline-item">
                        <div class="icon-marker {{ $classes['icon_bg'] }}">
                            <i class="fas {{ $alert->icon }} text-white"></i>
                        </div>
                        <div class="timeline-card dashboard-panel rounded-lg p-5 {{ $classes['bg'] }} {{ $classes['border'] }} {{ $classes['glow'] }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs font-semibold px-2 py-1 {{ $classes['icon_bg'] }} text-white rounded-md">{{ $alert->severity }}</span>
                                    <h3 class="font-bold text-lg text-white mt-2">{{ $alert->title }}</h3>
                                    <p class="text-sm text-gray-400"><i class="fas fa-map-marker-alt mr-1"></i> {{ $alert->location }}</p>
                                </div>
                                {{-- Zamanı Carbon ile "x dakika önce" formatında gösteriyoruz --}}
                                <time class="text-sm text-gray-500 whitespace-nowrap">{{ $alert->created_at->diffForHumans() }}</time>
                            </div>
                            <p class="text-gray-300 leading-snug my-3">{{ $alert->desc }}</p>
                            <div class="flex justify-end space-x-2">
                                <button class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-1 px-3 rounded-md text-xs">Arşivle</button>
                                <button class="bg-orange-600 hover:bg-orange-500 text-white font-semibold py-1 px-3 rounded-md text-xs">Haritada Göster</button>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- ... (uyarı yoksa kısmı aynı kalıyor) ... --}}
                @endforelse
            </div>
        </div>
        {{-- ... (sağ panel aynı kalıyor) ... --}}
    </div>
</div>
@endsection