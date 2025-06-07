<?php

namespace Src\Domain\User\Actions;

use Src\Domain\Settings\Data\ProfileUpdateData;
use Src\Domain\User\Models\User;

class UpdateUserProfileAction
{
    public function __invoke(User $user, ProfileUpdateData $profileData): User
    {
        $user->fill($profileData->toArray());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
} 