<?php
namespace App\Http\Responses;

use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\LoginResponse as ResponseContract;

class LoginResponse implements ResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
       return redirect()->intended(config('filament.home_url'));
    }

    public function __invoke($request): RedirectResponse|Redirector
    {
        return $this->toResponse($request);
    }
}