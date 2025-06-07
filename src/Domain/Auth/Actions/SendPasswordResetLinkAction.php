<?php

namespace Src\Domain\Auth\Actions;

use Illuminate\Support\Facades\Password;
use Src\Domain\Auth\Data\PasswordResetLinkData;

class SendPasswordResetLinkAction
{
    public function __invoke(PasswordResetLinkData $passwordResetLinkData): string
    {
        $status = Password::sendResetLink(
            $passwordResetLinkData->toArray()
        );

        return $status;
    }
} 