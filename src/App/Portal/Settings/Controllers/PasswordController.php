<?php

namespace App\Portal\Settings\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Domain\Settings\Actions\UpdatePasswordAction;
use Src\Domain\Settings\Data\PasswordUpdateData;
use Src\Support\Controllers\Controller;

class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $passwordData = PasswordUpdateData::from($request->all());

        app(UpdatePasswordAction::class)(auth()->user(), $passwordData);

        return back();
    }
}
