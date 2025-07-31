<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Yangın Yönetim Sistemi') | Türkyan</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { background: linear-gradient(to bottom right, #121212, #1E1E1E); font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #2C2C2C; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #555; border-radius: 10px; }
        @keyframes fadeInOut { 0%, 100% { opacity: 0; } 50% { opacity: 1; } }
        .alarm-blink { animation: fadeInOut 1.5s infinite; }
        #alarm-banner { top: 0; }
        body.has-navbar #alarm-banner { top: 3.5rem; }
    </style>
    @stack('styles') 
</head>
<body class="text-white {{ Auth::check() ? 'has-navbar' : '' }}">

    @auth
        {{-- KULLANICI GİRİŞ YAPMIŞSA --}}
        {{-- DÜZELTME: Navigasyon barına çok yüksek bir z-index değeri (z-[10000]) verildi --}}
        <nav class="bg-gray-900/80 backdrop-blur-md shadow-md py-2 relative z-[10000] h-[3.5rem]">
            <div class="container mx-auto px-4 flex justify-between items-center h-full">
                
                @if(Auth::user()->role == 'admin')
                    {{-- Sadece Admin için link olan logo ve tam menü --}}
                    <a href="{{ url('/') }}" class="text-orange-500 text-2xl font-bold flex items-center space-x-2">
                        <i class="fas fa-fire"></i>
                        <span>TÜRKYAN (Admin)</span>
                    </a>
                    <div class="hidden lg:flex items-center space-x-8">
                        <ul class="flex space-x-6 text-gray-300">
                            <li><a href="{{ route('index') }}" class="hover:text-orange-500">Ana Sayfa</a></li>
                            <li><a href="{{ route('harita') }}" class="hover:text-orange-500">Harita</a></li>
                            <li><a href="{{ route('uyarilar') }}" class="hover:text-orange-500">Uyarılar</a></li>
                            <li><a href="{{ route('raporlar') }}" class="hover:text-orange-500">Raporlar</a></li>
                            <li><a href="{{ route('hakkimizda') }}" class="hover:text-orange-500">Hakkımızda</a></li>
                        </ul>
                    </div>
                @else
                    {{-- Diğer roller için link olmayan logo --}}
                    <div class="text-orange-500 text-2xl font-bold flex items-center space-x-2">
                        <i class="fas fa-fire"></i>
                        <span>TÜRKYAN</span>
                    </div>
                @endif

                {{-- Sağ taraf kullanıcı menüsü (Tüm giriş yapmış kullanıcılar için ortak) --}}
                <div class="relative group">
                    <span class="text-gray-300 font-medium cursor-pointer">{{ Auth::user()->name }}</span>
                    <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md shadow-lg py-1 hidden group-hover:block">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-400 hover:bg-gray-700">Çıkış Yap</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @else
        {{-- MİSAFİRLER için GİRİŞ/KAYIT BARI --}}
        <nav class="bg-gray-900 bg-opacity-90 shadow-md py-2 relative z-[10000] h-[3.5rem]">
             <div class="container mx-auto px-4 flex justify-between items-center h-full">
                <a href="{{ url('/') }}" class="text-orange-500 text-2xl font-bold flex items-center space-x-2">
                    <i class="fas fa-fire"></i>
                    <span>TÜRKYAN</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-orange-500 font-semibold">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-md">Kayıt Ol</a>
                </div>
            </div>
        </nav>
    @endauth

    {{-- Alarm Banner'ı --}}
    @auth
        @if(in_array(Auth::user()->role, ['admin', 'itfaiye', 'bakanlik']))
        <div id="alarm-banner" class="fixed left-0 w-full bg-red-600 text-white text-center py-3 z-50 hidden shadow-lg flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-2xl mr-3 alarm-blink"></i>
            <span class="text-xl font-bold">⚠ YANGIN TESPİT EDİLDİ - KONUM: <span id="alarm-coords"></span></span>
        </div>
        @endif
    @endauth

    <div class="flex-grow">
        @yield('content')
    </div>

    @guest
    <footer class="bg-gray-900 bg-opacity-90 text-gray-400 text-center py-4 mt-auto">
        <p>&copy; {{ date('Y') }} Türkyan. Tüm Hakları Saklıdır.</p>
    </footer>
    @endguest
    
    @stack('scripts')
</body>
</html>