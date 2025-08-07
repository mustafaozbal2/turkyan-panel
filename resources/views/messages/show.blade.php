@extends('layouts.app')

@section('title', $recipient->name . ' ile Mesajlaşma')

@section('content')
<div class="container mx-auto py-8">
    <div class="dashboard-panel rounded-xl shadow-lg max-w-4xl mx-auto h-[calc(100vh-150px)] flex flex-col">
        <!-- Başlık -->
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-xl font-bold text-white">
                Konuşma: <span class="text-orange-400">{{ $recipient->name }}</span>
            </h2>
        </div>

        <!-- 🔧 Mesajlar -->
        <div id="message-container" class="flex-grow p-6 space-y-4 overflow-y-auto">
            @foreach($messages as $message)
                @if($message->sender_id == Auth::id())
                    <!-- Gönderen kişi -->
                    <div class="flex justify-end">
                        <div class="bg-orange-600 text-white p-3 rounded-lg max-w-xs lg:max-w-md">
                            <p>{{ $message->message }}</p>
                            <span class="text-xs text-orange-200 block text-right mt-1">
                                {{ $message->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @else
                    <!-- Alıcı kişi -->
                    <div class="flex justify-start">
                        <div class="bg-gray-700 text-gray-200 p-3 rounded-lg max-w-xs lg:max-w-md">
                            <p>{{ $message->message }}</p>
                            <span class="text-xs text-gray-400 block text-right mt-1">
                                {{ $message->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Yeni mesaj formu -->
        <div class="p-4 border-t border-gray-700">
            <form action="{{ route('messages.store') }}" method="POST">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $recipient->id }}">
                <div class="flex items-center">
                    <input 
                        type="text" 
                        name="message" 
                        class="form-input w-full rounded-full p-3 bg-gray-700 border-gray-600 text-white placeholder-gray-400" 
                        placeholder="Mesajınızı yazın..." 
                        autocomplete="off" 
                        required
                    >
                    <button 
                        type="submit" 
                        class="ml-4 bg-orange-600 hover:bg-orange-500 text-white rounded-full w-12 h-12 flex-shrink-0 flex items-center justify-center"
                    >
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🔧 Otomatik scroll scripti -->
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('message-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endsection
