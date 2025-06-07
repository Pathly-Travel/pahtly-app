<?php

namespace Src\Domain\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Src\Domain\Auth\Data\LoginData;

class LoginAction
{
    public function __invoke(LoginData $loginData): bool
    {
        if (! Auth::attempt($loginData->except('remember')->toArray(), $loginData->remember)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        return true;
    }
}
