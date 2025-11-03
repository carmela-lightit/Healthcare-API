<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Lightit\Doctors\Domain\Models\Doctor;

class ValidDoctorIds implements ValidationRule
{
    /**
     * @param array<int, int|string>|null                  $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value) || $value === []) {
            return;
        }

        $ids = array_map('intval', $value);

        $existingCount = Doctor::query()
            ->whereIn('id', $ids)
            ->count();

        if ($existingCount !== count($ids)) {
            $fail('One or more provided doctor IDs do not exist.');
        }
    }
}
