<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // checking admin
        if (Auth::user()->isAdmin()) {
            return redirect('/admin/dashboard');
        }

        return redirect('/dashboard');
    }
}
