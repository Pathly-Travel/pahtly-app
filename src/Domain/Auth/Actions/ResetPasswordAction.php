<?php

namespace Src\Domain\Auth\Actions;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Src\Domain\Auth\Data\NewPasswordData;

class ResetPasswordAction
{
    public function __invoke(NewPasswordData $newPasswordData): string
    {
        $status = Password::reset(
            $newPasswordData->toArray(),
            function ($user) use ($newPasswordData) {
                $user->forceFill([
                    'password' => Hash::make($newPasswordData->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status;
    }
} 