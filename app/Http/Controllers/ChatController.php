<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Events\ChatEvent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chats');
    }

    public function getMessage()
    {
        return Chat::with('user')->get();
    }

    public function sendMessage(Request $request)
    {
        $message = auth()->user()->chats()->create([
            'message'=> $request->message
        ]);

        broadcast(new ChatEvent($message->load('user')))->toOthers();

        return ['status' => 'success'];
    }
}
