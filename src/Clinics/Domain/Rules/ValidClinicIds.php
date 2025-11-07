<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Lightit\Clinics\Domain\Models\Clinic;

class ValidClinicIds implements ValidationRule
{
    /**
     * @param int|array<int, int|string>|null              $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        $ids = is_array($value) ? array_map('intval', $value) : [(int) $value];

        if ($ids === []) {
            return;
        }

        $existingCount = Clinic::query()
            ->whereIn('id', $ids)
            ->count();

        if ($existingCount !== count($ids)) {
            $fail('One or more provided clinic IDs do not exist.');
        }
    }
}
