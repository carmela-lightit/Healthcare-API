<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\DataTransferObjects;

final readonly class AppointmentDto
{
    public function __construct(
        public int $doctorId,
        public int $clinicId,
        public int $userId,
        public string $startsAt,
        public string $endsAt,
    ) {
    }
}
