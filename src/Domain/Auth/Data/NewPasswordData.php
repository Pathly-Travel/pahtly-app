<?php

namespace Src\Domain\Auth\Data;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class NewPasswordData extends Data
{
    public function __construct(
        #[Required]
        public string $token,

        #[Required, Email]
        public string $email,

        #[Required, Min(8), Confirmed]
        public string $password,

        #[Required]
        public string $password_confirmation,
    ) {}
} 