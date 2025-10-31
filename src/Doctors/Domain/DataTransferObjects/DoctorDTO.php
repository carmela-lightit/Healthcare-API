<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\DataTransferObjects;

final class DoctorDto
{
    /**
     * @param array<int> $clinicIds
     */
    public function __construct(
        public readonly string $name,
        public readonly array $clinicIds = [],
    ) {
    }
}
