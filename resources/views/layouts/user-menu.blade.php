@auth
    {{-- Bildirimler ve Mesajlar gibi ikonlar buraya eklenebilir --}}

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
@endauth