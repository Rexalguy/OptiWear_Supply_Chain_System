<?php
namespace App\Http;

use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\LoginResponse as ResponseContract;

class LoginResponse implements ResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
       return redirect()->route('customer');
    }

    public function __invoke($request): RedirectResponse|Redirector
    {
        return $this->toResponse($request);
    }
}