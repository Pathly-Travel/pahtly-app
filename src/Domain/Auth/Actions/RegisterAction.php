<?php

namespace Src\Domain\Auth\Actions;

use Src\Domain\Auth\Data\RegisterData;
use Src\Domain\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

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