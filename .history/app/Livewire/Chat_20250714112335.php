<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    protected static ?int $navigationSort = 3;
    protected $listeners = [
        'refreshUsers' => 'refreshUserList', // New event listener
    ];

    public $users;
    public $selectedUser;
    public $newMessage;
    public $messages;
    public $loginId;
    protected $rules = [
        'newMessage' => 'required|string|max:500',
        'selectedUser.id' => 'required|exists:users,id'
    ];

    public function mount()
    {
        $this->loginId = Auth::id();
        $this->refreshUserList();
        $this->loadInitialUser();
        $this->loadUsers();
        $this->loadMessages();

        if ($this->users->isNotEmpty()) {
            $this->selectedUser = $this->users->first();
            $this->loadMessages();
        }
    }

    public function submit()
    {
        // Validate only the message field
        $this->validateOnly('newMessage');

        if (!$this->selectedUser) {
            $this->addError('newMessage', 'Please select a recipient');
            return;
        }

        try {
            $message = ChatMessage::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $this->selectedUser->id,
                'message' => $this->newMessage,
            ]);

            $this->messages[] = $message;
            $this->newMessage = '';

            broadcast(new MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            $this->addError('newMessage', 'Failed to send message');
        }
    }
    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->loginId},MessageSent" => 'newChatMessageNotification',
        ];
    }
    public function newChatMessageNotification($message)
    {
        if ($message['sender_id'] == $this->selectedUser->id) {
            $messageObj = ChatMessage::find($message['id']);
            $this->messages->push($messageObj);
        }
    }
    protected function loadUsers()
    {
        $this->users = match (Auth::user()->role) {
            'customer', 'vendor', 'supplier' => User::where('role', 'manufacturer')
                ->where('id', '!=', $this->loginId)
                ->get(),
            default => User::whereNot('role', 'manufacturer')
                ->where('id', '!=', $this->loginId)
                ->get()
        };
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
                ->get(),
            default => User::whereNot('role', 'manufacturer')
                ->where('id', '!=', $this->loginId)
                ->get()
        } ?? collect();
    }
    protected function loadInitialUser()
    {
        $this->selectedUser = $this->users->first();
        if ($this->selectedUser) {
            $this->loadMessages();
        }
    }
    public function loadMessages()
    {
        $this->messages = ChatMessage::with(['sender', 'receiver'])
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                    ->where('receiver_id', $this->selectedUser->id);
            })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selectedUser->id)
                    ->where('receiver_id', Auth::id());
            })
            ->latest()
            ->take(100)
            ->get()
            ->reverse()
            ->values()
            ->all(); // <--- convert to plain array

    }
    public function render()
    {
        return view('livewire.chat');
    }
}
