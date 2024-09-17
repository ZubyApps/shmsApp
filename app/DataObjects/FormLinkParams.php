<?php

declare(strict_types = 1);

namespace App\DataObjects;

class FormLinkParams
{
    public function __construct(
        public readonly string $linkBaseUrl,
        public readonly int $sponsorCat,
        public readonly int $sponsor,
        public readonly string $cardNumber,
        public readonly string $patientType,
        public readonly string $phone,
        public readonly int $userId
    ) {
    }
}
