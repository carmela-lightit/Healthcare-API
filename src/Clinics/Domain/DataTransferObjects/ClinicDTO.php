<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\DataTransferObjects;

final class ClinicDto
{
    /**
     * @param array<int> $doctorIds
     */
    public function __construct(
        public readonly string $name,
        public readonly string $address,
        public readonly array $doctorIds = [],
    ) {
    }
}
