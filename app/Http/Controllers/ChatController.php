<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function loadChat()
    {
        return view('chat');
    }

    public function broadcastMsg(Request $request)
    {
        event(new MessageEvent($request->username, $request->message));
    }
}
