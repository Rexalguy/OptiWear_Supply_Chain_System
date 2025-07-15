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
    public function mount()
    {
        $this->loginId = Auth::id();
        if (Auth::User()->role == 'customer' || Auth::User()->role == 'vendor' || Auth::User()->role == 'supplier') {
            $this->users = User::where('role', 'manufacturer')->whereNot('id', Auth::id())->get() ?? [];
        } else {
            $this->users = User::whereNot('role', 'manufacturer')->get() ?? [];
        }
        if ($this->users->isEmpty()) {
            $this->selectedUser = null;
        } else {
            $this->selectedUser = $this->users->first() ?? null;
            $this->loadMessages();
        }
    }

    public function submit()
    {
        if (!$this->newMessage) {
            return;
        }
        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage,
        ]);
        $this->messages->push($message);
        $this->newMessage = '';
        broadcast(new MessageSent($message))->toOthers();
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
    public function loadMessages()
    {
        $this->messages = ChatMessage::query()->where(function ($q) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($q) {
            $q->where('sender_id', $this->selectedUser->id)->where('receiver_id', Auth::id());
        })->get();
    }
    public function render()
    {
        return view('livewire.chat');
    }
}
