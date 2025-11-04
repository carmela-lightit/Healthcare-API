<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\DataTransferObjects;

final readonly class DoctorDto
{
    /**
     * @param array<int> $clinicIds
     */
    public function __construct(
        public string $name,
        public array $clinicIds = [],
    ) {
    }
}
