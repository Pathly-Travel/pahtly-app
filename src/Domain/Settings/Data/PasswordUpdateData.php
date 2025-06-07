<?php

namespace Src\Domain\Settings\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Min;

class PasswordUpdateData extends Data
{
    public function __construct(
        #[Required]
        public string $currentPassword,
        
        #[Required, Min(8), Confirmed]
        public string $password,
        
        #[Required]
        public string $passwordConfirmation,
    ) {}
} 