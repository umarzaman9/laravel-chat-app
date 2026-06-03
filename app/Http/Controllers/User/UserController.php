<?php

namespace App\Http\Controllers\User;

use App\Events\MessageEvent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function loadDashboard(Request $request)
    {
        $users = User::whereNotIn('id', [auth()->id()])->get();
        return view('dashboard', compact('users'));
    }

    public function saveChat(Request $request)
    {
        try {
            $chat = Chat::create([
                'senderId' => $request->senderId,
                'receiverId' => $request->receiverId,
                'message' => $request->message,
            ]);

            event(new MessageEvent($chat));

            return response()->json([
                'success' => true,
                'data' => $chat,
            ]);
        } catch (Exception $e) {
            Log::error('saveChat error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
