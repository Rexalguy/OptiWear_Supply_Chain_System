<div>
    <div class="chat-container">
    <!-- Main Chat Layout -->
    <div class="chat-wrapper">
        <!-- Users List Sidebar -->
        <div class="users-sidebar">
            <div class="sidebar-header">
                Chats
            </div>
            <div class="users-list">
                @if($users->count() > 0)
                    @foreach($users as $user)
                    <div wire:click="selectUser({{ $user->id }})" 
                         class="user-item {{ $selectedUser->id === $user->id ? 'active' : '' }}">
                        <div class="user-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                        </div>
                        <div class="user-role {{ $user->role }}">
                            {{ ucfirst($user->role) }}
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-users">
                        No users found. Start a conversation!
                    </div>
                @endif
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="current-user-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div class="current-user-info">
                    <div class="user-name">{{ $selectedUser->name ?? 'No user' }}</div>
                    <div class="user-email">{{ $selectedUser->email ?? '' }}</div>
                </div>
            </div>

            <!-- Messages Container -->
            <div wire::pol.5s class="messages-container" 
                 x-data="{ atBottom: true }"
                 x-init="$watch('atBottom', value => value && $el.scrollTo({ top: $el.scrollHeight }))"
                 @scroll.debounce="atBottom = Math.abs($el.scrollHeight - $el.scrollTop - $el.clientHeight) < 50">
                @forelse($messages as $message)
                    <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                        <div class="message-bubble">
                            <div class="message-content">{{ $message->message }}</div>
                            <div class="message-meta">
                                <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                                @if($message->sender_id === auth()->id())
                                    <span class="message-status">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-messages">
                        No messages yet. Start the conversation!
                    </div>
                @endforelse
            </div>

            <!-- Message Input Form -->
            <form wire:submit.prevent="submit" class="message-form">
                <input type="text" 
                       wire:model.live.debounce.300ms="newMessage"
                       class="message-input"
                       placeholder="Type your message..."
                       x-ref="messageInput"
                       @keydown.enter.prevent="$wire.submit()">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="send-button">
                    <span wire:loading.remove>Send</span>
                    <span wire:loading wire:target="submit">
                        <svg class="spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        // Auto-scroll to bottom when new messages arrive
        Livewire.hook('commit', ({ component, commit, respond, succeed }) => {
            respond(() => {
                setTimeout(() => {
                    const container = document.querySelector('.messages-container');
                    if (container) container.scrollTop = container.scrollHeight;
                }, 50);
            });
        });

        // Play sound notification for new messages
        window.Echo?.private(`chat.${@js(auth()->id())}`)
            .listen('.message.sent', (e) => {
                if (e.sender.id !== @js(auth()->id())) {
                    new Audio('/notification.mp3').play().catch(() => {});
                }
            });
    });
</script>
@endscript

<style>
    /* Main Container */
    .chat-container {
        height: 100%;
        background-color: transparent;
    }

    .chat-wrapper {
        display: flex;
        height: 550px;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        overflow: hidden;
        background-color: white;
    }

    /* Users Sidebar */
    .users-sidebar {
        width: 25%;
        border-right: 1px solid #e5e7eb;
        background-color: #134b08;
        display: flex;
        flex-direction: column;
        color: white;
    }

    .sidebar-header {
        padding: 1rem;
        font-weight: 700;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        font-size: 1.125rem;
    }

    .users-list {
        flex: 1;
        overflow-y: auto;
    }

    .user-item {
        padding: 0.75rem;
        cursor: pointer;
        transition: background-color 0.2s;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .user-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .user-item.active {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .user-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }

    .user-avatar svg {
        width: 60%;
        height: 60%;
        color: #134b08;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 500;
    }

    .user-email {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .user-role {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        margin-top: 0.25rem;
    }

    /* Role-specific colors */
    .user-role.customer {
        background-color: #d1fae5;
        color: #065f46;
    }

    .user-role.manufacturer {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .user-role.vendor {
        background-color: #fef3c7;
        color: #92400e;
    }

    .user-role.supplier {
        background-color: #ede9fe;
        color: #5b21b6;
    }

    .empty-users {
        padding: 1rem;
        text-align: center;
        color: rgba(255, 255, 255, 0.7);
    }

    /* Chat Area */
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #f9fafb;
    }

    .chat-header {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 1rem;
        background-color: #134b08;
        color: white;
    }

    .current-user-avatar {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 50%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .current-user-avatar svg {
        width: 60%;
        height: 60%;
        color: #134b08;
    }

    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background-color: #f3f4f6;
    }

    .message {
        display: flex;
        margin-bottom: 0.75rem;
    }

    .message.sent {
        justify-content: flex-end;
    }

    .message.received {
        justify-content: flex-start;
    }

    .message-bubble {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        position: relative;
    }

    .message.sent .message-bubble {
        background-color: #3b82f6;
        color: white;
        border-bottom-right-radius: 0.25rem;
    }

    .message.received .message-bubble {
        background-color: #10b981;
        color: white;
        border-bottom-left-radius: 0.25rem;
    }

    .message-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.25rem;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        opacity: 0.8;
    }

    .message.sent .message-meta {
        color: rgba(255, 255, 255, 0.8);
    }

    .message-status svg {
        width: 0.75rem;
        height: 0.75rem;
    }

    .empty-messages {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-weight: 500;
    }

    /* Message Form */
    .message-form {
        display: flex;
        gap: 0.5rem;
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        background-color: white;
    }

    .message-input {
        flex: 1;
        border: 1px solid #d1d5db;
        border-radius: 9999px;
        padding: 0.75rem 1.25rem;
        outline: none;
        transition: all 0.2s;
    }

    .message-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .send-button {
        padding: 0.75rem 1.5rem;
        border-radius: 9999px;
        background-color: #134b08;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 5rem;
    }

    .send-button:hover {
        background-color: #0d3a06;
    }

    .send-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .spinner {
        animation: spin 1s linear infinite;
        width: 1.25rem;
        height: 1.25rem;
    }

    @keyframes spin {
        100% { transform: rotate(360deg); }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .chat-wrapper {
            background-color: #1f2937;
        }

        .chat-area {
            background-color: #111827;
        }

        .messages-container {
            background-color: #1f2937;
        }

        .message-input {
            background-color: #374151;
            color: white;
            border-color: #4b5563;
        }

        .message-form {
            background-color: #1f2937;
            border-top-color: #374151;
        }

        .empty-messages {
            color: #9ca3af;
        }
    }
</style>
</div>