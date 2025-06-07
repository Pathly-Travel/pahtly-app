<?php

namespace App\Portal\Settings\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Settings\Data\ProfileUpdateData;
use Src\Domain\User\Actions\DeleteUserAction;
use Src\Domain\User\Actions\UpdateUserProfileAction;
use Src\Support\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $profileData = ProfileUpdateData::from($request->all());

        app(UpdateUserProfileAction::class)($user, $profileData);

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        app(DeleteUserAction::class)($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
