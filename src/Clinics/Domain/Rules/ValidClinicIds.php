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
     * @param array<int, int|string>|null                  $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value) || $value === []) {
            return;
        }

        $ids = array_map(fn ($id): int => (int) $id, $value);

        $existingCount = Clinic::query()
            ->whereIn('id', $ids)
            ->count();

        if ($existingCount !== count($ids)) {
            $fail('One or more provided clinic IDs do not exist.');
        }
    }
}
