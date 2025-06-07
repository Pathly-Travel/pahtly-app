<?php

namespace Src\Domain\User\Actions;

use Illuminate\Support\Facades\Auth;
use Src\Domain\User\Models\User;

class DeleteUserAction
{
    public function __invoke(User $user): bool
    {
        Auth::logout();

        return $user->delete() ?? false;
    }
}
