<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    protected static string $view = 'filament.customer.pages.chat-page';
    protected static ?int $navigationSort = 7;

     public static function getNavigationBadge(): ?string
    {
        return static::getUnreadMessageCount() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info'; 
    }

    protected static function getUnreadMessageCount(): int
    {
        return ChatMessage::where('receiver_id', Auth::id())
            ->whereNull('is_read')
            ->count();
    }
}
