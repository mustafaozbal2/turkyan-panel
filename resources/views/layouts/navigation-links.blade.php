{{-- Active link styling --}}
@php
    $activeClass = 'text-orange-500 font-semibold';
    $inactiveClass = 'text-gray-300 hover:text-orange-500';
    $baseClass = 'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-300';
    $mobileBaseClass = 'block px-3 py-2 rounded-md text-base font-medium';
@endphp

@auth
    {{-- Tüm Rollerin Görebileceği Linkler --}}
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }}">Ana Panel</a>
    <a href="{{ route('uyarilar') }}" class="{{ request()->routeIs('uyarilar') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }}">Uyarılar</a>
    <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.index','news.show') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }}">Haberler</a>

    {{-- Sadece Admin ve İtfaiye Rollerinin Görebileceği Linkler --}}
    @if(in_array(Auth::user()->role, ['admin', 'itfaiye']))
        <a href="{{ route('reports.pending') }}" class="{{ request()->routeIs('reports.pending') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }}">İhbarlar</a>
        <a href="{{ route('harita') }}" class="{{ request()->routeIs('harita') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }}">Harita</a>
        <a href="{{ route('raporlar') }}" class="{{ request()->routeIs('raporlar') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }}">Genel Analizler</a>
    @endif

    {{-- Sadece Bakanlık ve Admin Rolleri İçin Haber Yönetimi Dropdown'ı --}}
    @if(in_array(Auth::user()->role, ['admin', 'bakanlik']))
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="{{ request()->routeIs('news.create') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }} flex items-center w-full text-left">
                <span>Haber Yönetimi</span>
                <i class="fas fa-chevron-down text-xs ml-1"></i>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition
                 class="lg:absolute lg:left-0 lg:mt-2 w-full lg:w-48 bg-gray-800 rounded-md shadow-lg py-1 z-50">
                <a href="{{ route('news.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Haberleri Listele</a>
                <a href="{{ route('news.create') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Yeni Haber Ekle</a>
            </div>
        </div>
    @endif

    {{-- Sadece Admin Rolünün Görebileceği Link --}}
    @if(Auth::user()->role == 'admin')
        <a href="{{ route('hakkimizda') }}" class="{{ request()->routeIs('hakkimizda') ? $activeClass : $inactiveClass }} {{ $baseClass }} {{ $mobileBaseClass }}">Hakkımızda</a>
    @endif
@endauth