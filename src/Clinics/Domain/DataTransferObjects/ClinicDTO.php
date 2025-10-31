<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\DataTransferObjects;

final readonly class ClinicDto
{
    /**
     * @param array<int> $doctorIds
     */
    public function __construct(
        public string $name,
        public string $address,
        public array $doctorIds,
    ) {
    }
}
