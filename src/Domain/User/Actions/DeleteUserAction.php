<?php

namespace Src\Domain\User\Actions;

use Src\Domain\User\Models\User;
use Illuminate\Support\Facades\Auth;

class DeleteUserAction
{
    public function __invoke(User $user): bool
    {
        Auth::logout();
        
        $deleted = $user->delete();
        
        return $deleted;
    }
} 