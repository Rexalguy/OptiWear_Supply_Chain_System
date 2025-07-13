<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    protected static ?int $navigationSort = 3;

    protected $listeners = [
        'refreshUsers' => 'refreshUserList',
    ];

    public $users = [];
    public $selectedUser;
    public $newMessage;
    public $messages = [];
    public $loginId;

    public function mount()
    {
        $this->loginId = Auth::id();
        $this->refreshUserList();
        $this->loadInitialUser();
    }

    public function submit()
    {
        $this->validate([
            'newMessage' => 'required|string|max:500',
            'selectedUser' => 'required|exists:users,id'
        ]);

        try {
            $message = ChatMessage::create([
                'sender_id' => $this->loginId,
                'receiver_id' => $this->selectedUser->id,
                'message' => $this->newMessage,
            ]);

            $message->load('sender', 'receiver');

            $this->messages[] = $message; // ✅ Array append
            $this->newMessage = '';

            broadcast(new MessageSent($message))->toOthers();

            return ['status' => 'Message sent'];
        } catch (\Exception $e) {
            $this->addError('newMessage', 'Failed to send message');
            return ['status' => 'error'];
        }
    }

    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->loginId},MessageSent" => 'newChatMessageNotification',
            "echo-private:chat.{$this->loginId},UserTyping" => 'showTypingIndicator',
        ];
    }

    public function newChatMessageNotification($message)
    {
        if ($message['sender_id'] == $this->selectedUser->id) {
            $msg = ChatMessage::find($message['id']);
            if ($msg) {
                $this->messages[] = $msg->load('sender', 'receiver'); // ✅ Array append
            }
        }
    }

    public function selectUser($id)
    {
        $this->selectedUser = User::find($id);
        $this->loadMessages();
    }

    public function refreshUserList()
    {
        $this->users = match (Auth::user()->role) {
            'customer', 'vendor', 'supplier' => User::where('role', 'manufacturer')
                ->where('id', '!=', $this->loginId)
                ->get()
                ->toArray(),
            default => User::whereNot('role', 'manufacturer')
                ->where('id', '!=', $this->loginId)
                ->get()
                ->toArray(),
        };
    }

    protected function loadInitialUser()
    {
        $this->selectedUser = collect($this->users)->first();
        if ($this->selectedUser) {
            $this->selectedUser = User::find($this->selectedUser['id']); // reload model
            $this->loadMessages();
        }
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::with(['sender', 'receiver'])
            ->where(function ($q) {
                $q->where('sender_id', $this->loginId)
                  ->where('receiver_id', $this->selectedUser->id);
            })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selectedUser->id)
                  ->where('receiver_id', $this->loginId);
            })
            ->latest()
            ->take(100)
            ->get()
            ->reverse()
            ->values()
            ->toArray(); // ✅ Important!
    }

    // Optional: called on keydown/input
    // public function typing()
    // {
    //     broadcast(new UserTyping($this->selectedUser->id, $this->loginId))->toOthers();
    // }

    // Optional: show “typing...” in the UI
    public function showTypingIndicator()
    {
        // Implement a simple flash indicator or set a public property like:
        // $this->isTyping = true;
    }

    public function render()
    {
        return view('livewire.chat');
    }
}