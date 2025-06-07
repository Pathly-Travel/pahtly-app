<?php

namespace Src\Domain\Auth\Actions;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Src\Domain\Auth\Data\RegisterData;
use Src\Domain\User\Models\User;

class RegisterAction
{
    public function __invoke(RegisterData $registerData): User
    {
        $user = User::create([
            'name' => $registerData->name,
            'email' => $registerData->email,
            'password' => Hash::make($registerData->password),
        ]);

        event(new Registered($user));

        return $user;
    }
}
