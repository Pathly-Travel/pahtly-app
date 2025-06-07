<?php

namespace App\Portal\Auth\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Auth\Actions\SendPasswordResetLinkAction;
use Src\Domain\Auth\Data\PasswordResetLinkData;
use Src\Support\Controllers\Controller;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request first to ensure proper error handling
        $request->validate([
            'email' => 'required|email',
        ]);

        $passwordResetLinkData = PasswordResetLinkData::from($request->all());

        app(SendPasswordResetLinkAction::class)($passwordResetLinkData);

        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
