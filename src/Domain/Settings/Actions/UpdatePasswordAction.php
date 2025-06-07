<?php

namespace Src\Domain\Settings\Actions;

use Src\Domain\Settings\Data\PasswordUpdateData;
use Src\Domain\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdatePasswordAction
{
    public function __invoke(User $user, PasswordUpdateData $passwordData): User
    {
        if (!Hash::check($passwordData->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($passwordData->password),
        ]);

        return $user;
    }
} 