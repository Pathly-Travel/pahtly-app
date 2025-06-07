<?php

namespace App\Portal\Auth\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Auth\Actions\ConfirmPasswordAction;
use Src\Domain\Auth\Data\ConfirmPasswordData;
use Src\Support\Controllers\Controller;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password page.
     */
    public function show(): Response
    {
        return Inertia::render('auth/ConfirmPassword');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Validate the request first to ensure proper error handling
        $request->validate([
            'password' => 'required',
        ]);

        $confirmPasswordData = ConfirmPasswordData::from($request->all());

        app(ConfirmPasswordAction::class)($user, $confirmPasswordData);

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
