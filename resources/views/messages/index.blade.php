@extends('layouts.app')

@section('title', 'Sohbetler')

@section('content')
<div class="container mx-auto py-6">
    <div class="max-w-3xl mx-auto bg-gray-800 rounded-xl shadow-lg p-6 space-y-6">

        <!-- 🔍 Arama kutusu -->
        <input
            type="text"
            id="userSearch"
            placeholder="İtfaiye adıyla ara..."
            class="w-full p-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none"
        />

        <!-- 📃 Sohbet listesi -->
        <div id="chatList" class="space-y-4 max-h-[70vh] overflow-y-auto">
            @foreach($allUsers as $user)
                @php
                    $thread = collect($threads)->firstWhere('user.id', $user->id);
                    $lastMessage = $thread['last_message'] ?? null;
                @endphp

                <a href="{{ route('chat.show', $user->id) }}" class="block p-4 rounded-lg bg-gray-700 hover:bg-gray-600 transition">
                    <div class="flex justify-between items-center">
                        <h3 class="text-white font-semibold">{{ $user->name }}</h3>
                        @if($lastMessage)
                            <span class="text-sm text-gray-400">{{ $lastMessage->created_at->format('H:i') }}</span>
                        @endif
                    </div>
                    @if($lastMessage)
                        <p class="text-sm text-gray-400 mt-1 truncate">{{ $lastMessage->message }}</p>
                    @else
                        <p class="text-sm text-gray-500 mt-1 italic">Henüz mesaj yok</p>
                    @endif
                </a>
            @endforeach
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('userSearch').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const chatItems = document.querySelectorAll('#chatList a');

        chatItems.forEach(item => {
            const name = item.querySelector('h3').textContent.toLowerCase();
            item.style.display = name.includes(query) ? 'block' : 'none';
        });
    });
</script>
@endpush
