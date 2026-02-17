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
        $query->where('from_user_id', $myId)
              ->where('to_user_id', $theirId);
    })->orWhere(function($query) use ($myId, $theirId) {
        $query->where('from_user_id', $theirId)
              ->where('to_user_id', $myId);
    })
    ->orderBy('created_at', 'asc')
    ->get();

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

    Message::create([
        'from_user_id' => Auth::id(),
        'to_user_id' => $request->receiver_id,
        'content' => $request->message,
    ]);

    return back()->with('success', 'Mesaj gönderildi.');
}


}