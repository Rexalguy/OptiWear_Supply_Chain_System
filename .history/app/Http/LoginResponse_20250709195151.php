<?php
namespace App\Http\Responses;

use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\LoginResponse as ResponseContract;

class LoginResponse implements ResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
       return redirect()->route('/customer')
    }
}