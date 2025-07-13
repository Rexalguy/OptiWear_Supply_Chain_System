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

//     public static function getNavigationBadge(): ?string
// {
//     return (string) ChatMessage::where('receiver_id', Auth::id())
//         ->where('is_read', false)
//         ->count();
// }

// public static function getNavigationBadgeColor(): ?string
// {
//     $unread = ChatMessage::where('receiver_id', Auth::id())
//         ->where('is_read', false)
//         ->count();

//     return $unread > 0 ? 'danger' : 'gray';
// }

// public static function getNavigationBadgeTooltip(): ?string
// {
//     return 'Unread messages';
// }
}
