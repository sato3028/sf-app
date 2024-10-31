<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Chat;
use App\Events\ChatEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function store(Request $request, $roomId)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $chat = Chat::create([
            'room_id' => $roomId,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        broadcast(new MessageSent($chat))->toOthers();
        Log::info('Message broadcasted', ['chat' => $chat]);

        // Inertiaレスポンスが必要な場合には空のレスポンスを返す
        return back(); // または return redirect()->route('rooms.show', $roomId);
    }
}

