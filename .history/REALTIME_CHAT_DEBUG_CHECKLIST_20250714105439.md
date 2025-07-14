# Laravel Reverb/Echo Real-Time Chat Debug Checklist

## 1. Reverb Server

-   Start the server:
    ```
    php artisan reverb:start
    ```
-   Watch the terminal for connection and broadcast logs.

## 2. .env Settings

```
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=local
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http
```

-   If you change .env, run:
    ```
    php artisan config:clear
    ```

## 3. Channel Authorization (routes/channels.php)

```php
Broadcast::channel('private-chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

## 4. Event (app/Events/MessageSent.php)

```php
use Illuminate\Support\Facades\Log;

public function broadcastOn(): array
{
    Log::info('Broadcasting MessageSent event', [
        'sender_id' => $this->message->sender_id,
        'receiver_id' => $this->message->receiver_id,
        'channels' => [
            "private-chat.{$this->message->receiver_id}",
            "private-chat.{$this->message->sender_id}"
        ]
    ]);
    return [
        new PrivateChannel("private-chat.{$this->message->receiver_id}"),
        new PrivateChannel("private-chat.{$this->message->sender_id}"),
    ];
}
```

## 5. Echo JS (resources/js/echo.js)

```js
import Echo from "laravel-echo";
window.Echo = new Echo({
    broadcaster: "reverb",
    wsHost: "127.0.0.1",
    wsPort: 6001,
    forceTLS: false,
    enabledTransports: ["ws"],
});
```

## 6. Blade (resources/views/filament/customer/pages/chat-page.blade.php)

```blade
@if(auth()->check())
<script>
    window.Laravel = {!! json_encode(['user' => auth()->user()]) !!};
    document.addEventListener('DOMContentLoaded', function() {
        function setupEchoChat() {
            if (window.Echo && window.Laravel && window.Laravel.user && window.Laravel.user.id) {
                window.Echo.private('private-chat.' + window.Laravel.user.id)
                    .listen('MessageSent', (e) => {
                        console.log('[Live Chat] MessageSent event received:', e);
                    });
                console.log('[Live Chat] Subscribed to: private-chat.' + window.Laravel.user.id);
            } else {
                console.warn('[Live Chat] Echo or user ID not available');
            }
        }
        setTimeout(setupEchoChat, 1000);
    });
</script>
@endif
```

## 7. Test Page

-   Use `/echo-reverb-test.html` to verify event delivery outside your app UI.

## 8. Laravel Queue (if used)

-   If `QUEUE_CONNECTION` is not `sync`, run:
    ```
    php artisan queue:work
    ```

## 9. Debug

-   Check `storage/logs/laravel.log` for the broadcast log.
-   Check Reverb server terminal for activity.
-   Check browser console for `[Live Chat] MessageSent event received:`.

---

**If you follow every step above and still have no real-time events, the issue is with your environment or network.**
