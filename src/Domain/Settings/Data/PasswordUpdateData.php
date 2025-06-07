<?php

namespace Src\Domain\Settings\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\MapName;

class PasswordUpdateData extends Data
{
    public function __construct(
        #[Required]
        #[MapName('current_password')]
        public string $currentPassword,
        
        #[Required, Min(8), Confirmed]
        public string $password,
        
        #[Required]
        #[MapName('password_confirmation')]
        public string $passwordConfirmation,
    ) {}
} 