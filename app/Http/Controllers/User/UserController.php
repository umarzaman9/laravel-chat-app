<?php

namespace App\Http\Controllers\User;

use App\Events\MessageEvent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Notifications\ChatSent;
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

            $receiver = User::find($request->receiverId);
            if ($receiver) {
                $receiver->notify(new ChatSent($chat));
            }

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

    public function loadChats(Request $request)
    {
        try {
            $chats = Chat::where(function ($q) use ($request) {
                $q->where('senderId', '=', $request->senderId)
                    ->orWhere('senderId', '=', $request->receiverId);
            })->where(function ($q1) use ($request) {
                $q1->where('receiverId', '=', $request->senderId)
                    ->orWhere('receiverId', '=', $request->receiverId);
            })->get();

            return response()->json([
                'success' => true,
                'data' => $chats,
            ]);
        } catch (Exception $e) {
            Log::error('saveChat error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // ── 3 NEW METHODS BELOW ────────────────────────────────────────

    // Returns unread notifications for whoever is logged in.
    // unreadNotifications is a property the Notifiable trait adds to
    // every User — it's just a scoped Eloquent relationship filtering
    // rows in the notifications table where notifiable_id = auth user
    // AND read_at IS NULL.
    public function getNotifications(Request $request)
    {
        $notifications = auth()->user()->unreadNotifications;

        return response()->json([
            'success' => true,
            'data'    => $notifications,
            'count'   => $notifications->count(),
        ]);
    }

    // Marks one notification read by setting read_at = now().
    // The UUID id comes from the URL: /notifications/{id}/read
    public function markNotificationRead(Request $request, $id)
    {
        $notification = auth()->user()
            ->unreadNotifications
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();   // built-in method, sets read_at
        }

        return response()->json(['success' => true]);
    }

    // Bulk-marks everything unread as read in one query.
    public function markAllRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
