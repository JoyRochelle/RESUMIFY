<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:png,jpg,webp,jpeg', 'max:2048']
        ]);

        $user = auth()->user();

        // Delete old avatar if exists (use raw DB value, not the accessor URL)
        $rawAvatar = $user->getRawOriginal('avatar_url');
        if ($rawAvatar) {
            Storage::disk('public')->delete($rawAvatar);
        }

        // store new avatar
        $path = $request->file('avatar')->store('avatar', 'public');

        $user->update([
            'avatar_url' => $path
        ]);

        return back()->with('status', 'avatar-updated');
    }

    /**
     * Delete the user's avatar.
     */

    public function deleteAvatar()
    {
        $user = auth()->user();

        $rawAvatar = $user->getRawOriginal('avatar_url');
        if ($rawAvatar) {
            Storage::disk('public')->delete($rawAvatar);
            $user->update(['avatar_url' => null]);
        }

        return back()->with('status', 'avatar-deleted');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password']
        ]);

        $user = auth()->user();

        // Skip deleting avatar file to allow full account restoration later
        // via a cleanup job if permanent deletion is requested.

        auth()->logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'account-deleted');

    }
}
