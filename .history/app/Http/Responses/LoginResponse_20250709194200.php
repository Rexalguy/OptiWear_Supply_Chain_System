<?php
namespace App\Http\Responses;

use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;

class LoginResponse implements ResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // Example: Redirect admin users to the admin panel
        if (auth()->user()->hasRole('admin')) {
            return redirect('/admin');
        }

        // Default redirection (e.g., user panel)
        return redirect('/user');
    }
}