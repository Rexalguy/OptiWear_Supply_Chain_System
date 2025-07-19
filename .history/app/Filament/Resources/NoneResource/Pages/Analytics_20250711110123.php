<?php

namespace App\Filament\Resources\NoneResource\Pages;

use App\Filament\Resources\NoneResource;
use Filament\Resources\Pages\Page;

class Analytics extends Page
{
    protected static string $resource = NoneResource::class;

    protected static string $view = 'filament.resources.none-resource.pages.analytics';
}
