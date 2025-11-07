<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Lightit\Doctors\Domain\Models\Doctor;

final class DoctorClinicAssignmentRule implements ValidationRule
{
    public function __construct(
        private readonly int $doctorId,
        private readonly int $clinicId,
    ) {
    }

    /**
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var Doctor|null $doctor */
        $doctor = Doctor::query()
            ->with('clinics:id')
            ->find($this->doctorId);

        if (! $doctor) {
            $fail('The selected doctor does not exist.');

            return;
        }

        $clinicIds = $doctor->clinics->pluck('id')->toArray();

        if (! in_array($this->clinicId, $clinicIds, true)) {
            $fail('The selected clinic is not assigned to this doctor.');
        }
    }
}
