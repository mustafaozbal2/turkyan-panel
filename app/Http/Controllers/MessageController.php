<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    // Belirli bir kullanıcı ile olan konuşmayı gösterir
    public function show(User $user)
    {
        $myId = Auth::id();
        $theirId = $user->id;

        $messages = Message::where(function($query) use ($myId, $theirId) {
            $query->where('sender_id', $myId)->where('receiver_id', $theirId);
        })->orWhere(function($query) use ($myId, $theirId) {
            $query->where('sender_id', $theirId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->get();

        return view('messages.show', [
            'recipient' => $user,
            'messages' => $messages
        ]);
    }

    // Yeni bir mesaj gönderir
   public function store(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|integer|exists:users,id',
        'message' => 'required|string',
    ]);

    $message = Message::create([
        'sender_id' => Auth::id(),
        'receiver_id' => $request->receiver_id,
        'message' => $request->message,
    ]);

    if ($message) {
        return redirect()->back()->with('success', 'Mesaj gönderildi.');
    } else {
        return redirect()->back()->with('error', 'Mesaj gönderilemedi.');
    }
}

}