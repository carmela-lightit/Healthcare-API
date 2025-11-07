<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Rules;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Translation\PotentiallyTranslatedString;
use Lightit\Appointments\Domain\Models\Appointment;

final class NoUserAppointmentOverlapRule implements ValidationRule
{
    public function __construct(
        private readonly int $userId,
        private readonly CarbonImmutable $startsAt,
        private readonly CarbonImmutable $endsAt,
    ) {
    }

    /**
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $overlap = Appointment::query()
            ->where('user_id', $this->userId)
            ->where(function (Builder $query): void {
                $query
                    ->whereBetween('starts_at', [$this->startsAt, $this->endsAt])
                    ->orWhereBetween('ends_at', [$this->startsAt, $this->endsAt])
                    ->orWhere(
                        function (Builder $q): void {
                            $q->where('starts_at', '<=', $this->startsAt)
                              ->where('ends_at', '>=', $this->endsAt);
                        }
                    );
            })
            ->exists();

        if ($overlap) {
            $fail('You already have an overlapping appointment during this time range.');
        }
    }
}
