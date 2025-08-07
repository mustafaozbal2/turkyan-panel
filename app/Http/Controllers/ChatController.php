<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
   public function index()
{
    $user = Auth::user();

    if ($user->role !== 'bakanlik') {
        return redirect()->route('dashboard');
    }

   $userId = auth()->id();

$conversations = Message::selectRaw('
        CASE 
            WHEN sender_id = ? THEN receiver_id
            ELSE sender_id
        END as user_id, MAX(created_at) as last_message_at', [$userId])
    ->where(function($q) use ($userId) {
        $q->where('sender_id', $userId)
          ->orWhere('receiver_id', $userId);
    })
    ->groupBy('user_id')
    ->orderByDesc('last_message_at')
    ->get();

// Sonrasında kullanıcıları ve son mesajları birlikte göstermek istersen:
$conversations = Message::where('sender_id', Auth::id())
    ->orWhere('receiver_id', Auth::id())
    ->orderBy('created_at', 'desc')
    ->get()
    ->groupBy(function ($message) {
        return $message->sender_id == Auth::id() ? $message->receiver_id : $message->sender_id;
    });


    $threads = [];

foreach ($conversations as $participantId => $messages) {
    $messages = collect($messages); // Her ihtimale karşı array to collection

    if ($messages->isEmpty()) {
        continue; // Mesaj yoksa bu kullanıcıyı listeleme
    }

    $lastMessage = $messages->sortByDesc('created_at')->first();
    $participant = User::find($participantId);

    if ($participant) {
        $threads[] = [
            'user' => $participant,
            'last_message' => $lastMessage
        ];
    }
}


    // Tüm itfaiyeleri al (arama için)
    $allUsers = User::where('role', 'itfaiye')->get();

    // Listeyi tarihe göre sırala
    usort($threads, fn ($a, $b) =>
        $b['last_message']->created_at->timestamp - $a['last_message']->created_at->timestamp
    );

    return view('messages.index', compact('threads', 'allUsers'));
}


    public function show($id)
{
    $user = Auth::user();
    $recipient = \App\Models\User::findOrFail($id);

    // Mesajları al
    $messages = Message::where(function ($query) use ($user, $recipient) {
        $query->where('sender_id', $user->id)
              ->where('receiver_id', $recipient->id);
    })->orWhere(function ($query) use ($user, $recipient) {
        $query->where('sender_id', $recipient->id)
              ->where('receiver_id', $user->id);
    })->orderBy('created_at')->get();

    // Gelen okunmamış mesajları okundu olarak işaretle
    Message::where('sender_id', $recipient->id)
        ->where('receiver_id', $user->id)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return view('messages.show', [
        'recipient' => $recipient,
        'messages' => $messages,
    ]);
}

}
