<?php

declare(strict_types = 1);

namespace App\DataObjects;

class SponsorCategoryDto
{
    public function __construct(
        public readonly bool $isNhis = false,
        public readonly bool $isHmo = false,
        public readonly bool $isRetainership = false,
        public readonly bool $isIndividual = false,
        public readonly bool $isFamily = false,
    ) {
}
}
