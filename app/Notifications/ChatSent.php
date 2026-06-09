<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatSent extends Notification
{
    use Queueable;

    protected $chat;

    // Constructor now RECEIVES the chat model
    // When you call ->notify(new ChatSent($chat)), Laravel passes $chat here
    public function __construct($chat)
    {
        $this->chat = $chat;
    }

    // via() = WHICH channel to use
    // 'database' means: store a row in the notifications table
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // toDatabase() = WHAT to store in the JSON `data` column
    // $notifiable here is the receiver's User model
    // Return whatever your frontend needs to display the notification
    public function toDatabase(object $notifiable): array
    {
        return [
            'senderId'   => $this->chat->senderId,
            'receiverId' => $this->chat->receiverId,
            'message'    => $this->chat->message,
            'chatId'     => $this->chat->id,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
