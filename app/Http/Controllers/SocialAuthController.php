<?php

namespace App\Http\Controllers;

use App\Models\OauthProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google OAuth callback.
     */
    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['oauth' => 'Unable to authenticate with Google. Please try again.']);
        }

        // Find existing OAuth link
        $oauthProvider = OauthProvider::where('provider', 'google')
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($oauthProvider) {
            // Existing OAuth link — update token and login
            $oauthProvider->update(['token' => $socialUser->token]);
            $user = $oauthProvider->user;
        } else {
            // Find or create user by email
            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {
                // Create new user (no password, email auto-verified)
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => null,
                    'avatar_url' => $socialUser->getAvatar(),
                ]);

                $user->markEmailAsVerified();
            }

            // Link Google to the user
            $user->oauthProviders()->create([
                'provider' => 'google',
                'provider_id' => $socialUser->getId(),
                'token' => $socialUser->token,
            ]);

            // Update avatar if the user doesn't have one
            if (! $user->avatar_url && $socialUser->getAvatar()) {
                $user->update(['avatar_url' => $socialUser->getAvatar()]);
            }
        }

        Auth::login($user, remember: true);

        // Role-based redirect
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/dashboard');
    }
}
