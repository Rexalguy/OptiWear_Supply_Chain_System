<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('getstarted');
})->name('home');
Route::rollback(fn () => view('cus'))
    ->name('getstarted');