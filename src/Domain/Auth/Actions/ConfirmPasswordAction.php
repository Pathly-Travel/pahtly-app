<?php

namespace Src\Domain\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Src\Domain\Auth\Data\ConfirmPasswordData;
use Src\Domain\User\Models\User;

class ConfirmPasswordAction
{
    public function __invoke(User $user, ConfirmPasswordData $confirmPasswordData): bool
    {
        if (! Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $confirmPasswordData->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        return true;
    }
} 