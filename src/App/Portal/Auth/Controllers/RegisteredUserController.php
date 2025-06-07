<?php

namespace App\Portal\Auth\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Auth\Actions\RegisterAction;
use Src\Domain\Auth\Data\RegisterData;
use Src\Domain\User\Models\User;
use Src\Support\Controllers\Controller;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request first to ensure proper error handling
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $registerData = RegisterData::from($request->all());

        $user = app(RegisterAction::class)($registerData);

        Auth::login($user);

        return to_route('dashboard');
    }
}
