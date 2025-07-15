<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public ChatMessage $message) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->message->receiver_id}");
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
