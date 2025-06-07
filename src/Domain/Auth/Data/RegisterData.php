<?php

namespace Src\Domain\Auth\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;

class RegisterData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $name,
        
        #[Required, Email, Max(255)]
        public string $email,
        
        #[Required, Min(8), Confirmed]
        public string $password,
        
        #[Required]
        public string $passwordConfirmation,
    ) {}
} 