<?php

namespace Src\Domain\Auth\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PasswordResetLinkData extends Data
{
    public function __construct(
        #[Required, Email]
        public string $email,
    ) {}
} 