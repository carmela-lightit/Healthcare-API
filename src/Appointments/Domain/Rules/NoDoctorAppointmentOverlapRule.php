<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Translation\PotentiallyTranslatedString;
use Lightit\Appointments\Domain\Models\Appointment;

final class NoDoctorAppointmentOverlapRule implements ValidationRule
{
    public function __construct(private readonly Request $request)
    {
    }

    /**
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $doctorId = $this->request->input('doctor_id');
        $startsAt = $this->request->input('starts_at');
        $endsAt = $this->request->input('ends_at');

        if (! $doctorId || ! $startsAt || ! $endsAt) {
            return;
        }

        $overlap = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where(function (Builder $query) use ($startsAt, $endsAt): void {
                $query
                    ->whereBetween('starts_at', [$startsAt, $endsAt])
                    ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                    ->orWhere(
                        function (Builder $q) use ($startsAt, $endsAt): void {
                            $q->where('starts_at', '<=', $startsAt)
                              ->where('ends_at', '>=', $endsAt);
                        }
                    );
            })
            ->exists();

        if ($overlap) {
            $fail('The doctor already has an overlapping appointment during this time range.');
        }
    }
}
