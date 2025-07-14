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


    #[Rule('required|string|max:500')] ;// Add validation rule
    public $newMessage;
    public $users;
    public $selectedUser;
    public $newMessage;
    public $messages;
    public $loginId;
    public function mount()
    {
        $this->loginId = Auth::id();
        $this->refreshUserList(); 
        $this->loadInitialUser();
    }

    public function submit()
    {
        // Add proper validation rules
        $this->validate([
            'newMessage' => 'required|string|max:500',
            'selectedUser' => 'required|exists:users,id'
        ]);

        try {
            $message = ChatMessage::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $this->selectedUser->id,
                'message' => $this->newMessage,
            ]);

            $this->messages->push($message);
            $this->newMessage = '';

            broadcast(new MessageSent($message))->toOthers();

            // Return success response
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
        ];
    }
    public function newChatMessageNotification($message)
    {
        if ($message['sender_id'] == $this->selectedUser->id) {
            $messageObj = ChatMessage::find($message['id']);
            $this->messages->push($messageObj);
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
            ->take(100) // Prevent overload
            ->get()
            ->reverse(); // Show newest at bottom
    }
    public function render()
    {
        return view('livewire.chat');
    }
}