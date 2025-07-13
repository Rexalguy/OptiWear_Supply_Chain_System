<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    public function __construct(public ChatMessage $message) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("chat.{$this->message->receiver_id}"),
            new PrivateChannel("chat.{$this->message->sender_id}"), // Also notify sender
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => htmlspecialchars($this->message->message), // XSS protection
            'created_at' => $this->message->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->message->sender_id,
                'name' => $this->message->sender->name,
            ],
            'receiver_id' => $this->message->receiver_id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent'; // Standardized event name
    }
}
