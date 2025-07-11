<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class ChatPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    protected static string $view = 'filament.customer.pages.chat-page';
    protected static ?int $navigationSort = 1;
}
