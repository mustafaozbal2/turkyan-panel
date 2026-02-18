<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Yangın Yönetim Sistemi') | Türkyan</title>

    {{-- Kütüphaneler --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #121212; font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        .leaflet-container { z-index: 10; }
    </style>
    @stack('styles')
</head>
<body class="text-white antialiased">

    {{-- Ana Navigasyon Bloğu --}}
    {{-- DÜZELTME 1: z-index değeri z-40'tan z-50'ye yükseltildi. Bu, navbar'ın içeriğin altına kaymasını engeller. --}}
    <div x-data="{ mobileMenuOpen: false }" class="bg-gray-900/80 backdrop-blur-md shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="/" class="text-orange-500 text-2xl font-bold flex items-center space-x-2 flex-shrink-0">
                    <i class="fas fa-fire"></i>
                    <span>TÜRKYAN</span>
                </a>

                {{-- MASAÜSTÜ MENÜSÜ --}}
                <div class="hidden lg:flex items-center space-x-1">
                    @auth
                        {{-- DÜZELTME 2: 'navigation-links.blade.php' içeriği buraya eklendi --}}
                        @php
                            $activeClass = 'text-orange-500 font-semibold';
                            $inactiveClass = 'text-gray-300 hover:text-orange-500';
                            $baseClass = 'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-300';
                        @endphp
                        {{-- Tüm Rollerin Görebileceği Linkler --}}
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $baseClass }}">Ana Panel</a>
                        <a href="{{ route('uyarilar') }}" class="{{ request()->routeIs('uyarilar') ? $activeClass : $inactiveClass }} {{ $baseClass }}">Uyarılar</a>
                        <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.index','news.show') ? $activeClass : $inactiveClass }} {{ $baseClass }}">Haberler</a>

                        {{-- Sadece Admin ve İtfaiye Rollerinin Görebileceği Linkler --}}
                        @if(in_array(Auth::user()->role, ['admin', 'itfaiye']))
                            <a href="{{ route('reports.pending') }}" class="{{ request()->routeIs('reports.pending') ? $activeClass : $inactiveClass }} {{ $baseClass }}">İhbarlar</a>
                            <a href="{{ route('harita') }}" class="{{ request()->routeIs('harita') ? $activeClass : $inactiveClass }} {{ $baseClass }}">Harita</a>
                            <a href="{{ route('raporlar') }}" class="{{ request()->routeIs('raporlar') ? $activeClass : $inactiveClass }} {{ $baseClass }}">Genel Analizler</a>
                        @endif

                        {{-- Sadece Bakanlık ve Admin Rolleri İçin Haber Yönetimi Dropdown'ı --}}
                        @if(in_array(Auth::user()->role, ['admin', 'bakanlik']))
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="{{ request()->routeIs('news.create','news.edit') ? $activeClass : $inactiveClass }} {{ $baseClass }} flex items-center w-full text-left">
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
                            <a href="{{ route('hakkimizda') }}" class="{{ request()->routeIs('hakkimizda') ? $activeClass : $inactiveClass }} {{ $baseClass }}">Hakkımızda</a>
                        @endif
                    @endauth
                </div>
                
                <div class="hidden lg:flex items-center space-x-6">
                     @auth
                        {{-- Mesajlaşma İkonu --}}
                        @if(!in_array(Auth::user()->role, ['user']))
                            @php
                                $user = Auth::user();
                                $bakanlik = \App\Models\User::where('role', 'bakanlik')->first();
                                $unreadCount = 0;
                                if ($user && $user->role !== 'bakanlik' && $bakanlik) {
                                    $unreadCount = \App\Models\Message::where('sender_id', $bakanlik->id)
                                        ->where('receiver_id', $user->id)
                                        ->where('is_read', false)
                                        ->count();
                                }
                            @endphp
                            <a href="{{ $user->role === 'bakanlik' ? route('chat.index') : route('chat.show', $bakanlik->id ?? 0) }}" class="relative text-gray-300 hover:text-white">
                                <i class="fas fa-comments text-xl"></i>
                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full animate-pulse">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        @endif

                        {{-- Kullanıcı Adı ve Çıkış Dropdown'ı --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-300 focus:outline-none w-full text-left lg:w-auto px-3 py-2">
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak x-transition
                                 class="lg:absolute lg:right-0 mt-2 w-full lg:w-48 bg-gray-800 rounded-md shadow-lg py-1 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-400 hover:bg-gray-700">Çıkış Yap</button>
                                </form>
                            </div>
                        </div>
                     @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-orange-500 font-semibold">Giriş Yap</a>
                        <a href="{{ route('register') }}" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-md">Kayıt Ol</a>
                     @endauth
                </div>

                {{-- Mobil Menü Butonu (Hamburger) --}}
                <div class="lg:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path x-show="!mobileMenuOpen" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path x-show="mobileMenuOpen" x-cloak class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </nav>

        {{-- MOBİL MENÜ PANELİ --}}
        <div x-show="mobileMenuOpen" x-cloak @click.away="mobileMenuOpen = false" x-transition class="lg:hidden absolute w-full bg-gray-800 shadow-lg z-30">
            <div class="flex flex-col p-4 space-y-2 text-gray-300">
                @auth
                    {{-- DÜZELTME 3: Mobil menü linkleri de buraya eklendi --}}
                    @php
                        $activeClass = 'text-orange-500 font-semibold';
                        $inactiveClass = 'text-gray-300 hover:text-orange-500';
                        $mobileBaseClass = 'block px-3 py-2 rounded-md text-base font-medium';
                    @endphp
                    {{-- Tüm Rollerin Görebileceği Linkler --}}
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }}">Ana Panel</a>
                    <a href="{{ route('uyarilar') }}" class="{{ request()->routeIs('uyarilar') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }}">Uyarılar</a>
                    <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.index','news.show') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }}">Haberler</a>

                    {{-- Sadece Admin ve İtfaiye Rollerinin Görebileceği Linkler --}}
                    @if(in_array(Auth::user()->role, ['admin', 'itfaiye']))
                        <a href="{{ route('reports.pending') }}" class="{{ request()->routeIs('reports.pending') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }}">İhbarlar</a>
                        <a href="{{ route('harita') }}" class="{{ request()->routeIs('harita') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }}">Harita</a>
                        <a href="{{ route('raporlar') }}" class="{{ request()->routeIs('raporlar') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }}">Genel Analizler</a>
                    @endif

                    {{-- Sadece Bakanlık ve Admin Rolleri İçin Haber Yönetimi Dropdown'ı --}}
                    @if(in_array(Auth::user()->role, ['admin', 'bakanlik']))
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="{{ request()->routeIs('news.create','news.edit') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }} flex items-center w-full text-left">
                                <span>Haber Yönetimi</span>
                                <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak x-transition
                                 class="w-full pl-4">
                                <a href="{{ route('news.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Haberleri Listele</a>
                                <a href="{{ route('news.create') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Yeni Haber Ekle</a>
                            </div>
                        </div>
                    @endif

                    {{-- Sadece Admin Rolünün Görebileceği Link --}}
                    @if(Auth::user()->role == 'admin')
                        <a href="{{ route('hakkimizda') }}" class="{{ request()->routeIs('hakkimizda') ? $activeClass : $inactiveClass }} {{ $mobileBaseClass }}">Hakkımızda</a>
                    @endif
                    
                    <hr class="border-gray-700 my-2">

                    {{-- Mobil Kullanıcı Menüsü --}}
                    <div class="font-semibold text-white px-3 py-2">{{ Auth::user()->name }}</div>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-red-400 hover:bg-gray-700 hover:text-white">Çıkış Yap</button>
                    </form>

                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Kayıt Ol</a>
                @endauth
            </div>
        </div>
    </div>

    <main class="flex-grow relative">@yield('content')</main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @stack('scripts')
    @auth <script type="module"> /* Echo scriptleriniz */ </script> @endauth
</body>
</html>