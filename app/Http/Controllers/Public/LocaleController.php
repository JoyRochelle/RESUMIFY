<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     *
     * Guests: stored in session. Authenticated users: persisted on the account.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, config('app.supported_locales', ['en']), true), 404);

        if ($user = $request->user()) {
            $user->update(['locale' => $locale]);
        }

        $request->session()->put('locale', $locale);

        return back();
    }
}
