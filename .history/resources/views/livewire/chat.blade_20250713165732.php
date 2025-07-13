<div>
    <!-- Add this at the top -->
    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Auto-scroll to bottom
            Livewire.hook('commit', ({ component, commit, respond, succeed }) => {
                respond(() => {
                    setTimeout(() => {
                        const container = document.querySelector('.overflow-y-auto');
                        if (container) container.scrollTop = container.scrollHeight;
                    }, 50);
                });
            });

            // Message sound notification
            window.Echo?.private(`chat.${@js(auth()->id())}`)
                .listen('.message.sent', (e) => {
                    if (e.sender.id !== @js(auth()->id())) {
                        new Audio('/notification.mp3').play().catch(() => {});
                    }
                });
        });
    </script>
    @endscript

    <!-- Updated messages section -->
    <div class="flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto" 
         x-data="{ atBottom: true }"
         x-init="$watch('atBottom', value => value && $el.scrollTo({ top: $el.scrollHeight }))"
         @scroll.debounce="atBottom = Math.abs($el.scrollHeight - $el.scrollTop - $el.clientHeight) < 50">
         
        @forelse($messages as $message)
            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} px-4 py-2">
                <div class="max-w-[70%] relative">
                    <!-- Message bubble -->
                    <div @class([
                        'rounded-lg p-3 shadow',
                        'bg-blue-600 text-white' => $message->sender_id === auth()->id(),
                        'bg-green-700 text-white' => $message->sender_id !== auth()->id(),
                    ])>
                        {{ $message->message }}
                        
                        <!-- Message metadata -->
                        <div class="text-xs mt-1 opacity-80 flex items-center justify-end gap-1">
                            <span>{{ $message->created_at->diffForHumans() }}</span>
                            @if($message->sender_id === auth()->id())
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="h-full flex items-center justify-center">
                <div class="text-center p-6 text-gray-500">
                    No messages yet. Say hello!
                </div>
            </div>
        @endforelse
    </div>

    <!-- Enhanced input form -->
    <form wire:submit.prevent="submit" class="p-3 border-t bg-white dark:bg-gray-800 flex gap-2">
        <input
            wire:model.live.debounce.300ms="newMessage"
            type="text"
            class="flex-1 rounded-full px-4 py-2 border focus:ring-2 focus:ring-blue-500 transition"
            placeholder="Type your message..."
            x-ref="messageInput"
            @keydown.enter.prevent="$wire.submit()"
        >
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="shrink-0 px-4 py-2 rounded-full bg-green-600 text-white hover:bg-green-700 transition disabled:opacity-50"
        >
            <span wire:loading.remove>Send</span>
            <span wire:loading wire:target="submit">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>
    </form>
</div>