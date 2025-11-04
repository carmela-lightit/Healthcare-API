<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Rules\DoctorClinicAssignmentRule;
use Lightit\Appointments\Domain\Rules\NoDoctorAppointmentOverlapRule;
use Lightit\Appointments\Domain\Rules\NoUserAppointmentOverlapRule;
use Lightit\Clinics\Domain\Rules\ValidClinicIds;
use Lightit\Doctors\Domain\Rules\ValidDoctorIds;

final class UpsertAppointmentRequest extends FormRequest
{
    public const string DOCTOR_ID = 'doctor_id';

    public const string CLINIC_ID = 'clinic_id';

    public const string STARTS_AT = 'starts_at';

    public const string ENDS_AT = 'ends_at';

    /**
     * @return array<string, list<string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            self::DOCTOR_ID => ['required', 'integer', new ValidDoctorIds(), new DoctorClinicAssignmentRule($this)],
            self::CLINIC_ID => ['required', 'integer', new ValidClinicIds()],
            self::STARTS_AT => ['required', 'date', 'after_or_equal:now',
                new NoDoctorAppointmentOverlapRule($this),
                new NoUserAppointmentOverlapRule($this)],
            self::ENDS_AT => ['required', 'date', 'after:' . self::STARTS_AT],
        ];
    }

    public function toDto(): AppointmentDto
    {
        return new AppointmentDto(
            doctorId: (int) $this->integer(self::DOCTOR_ID),
            clinicId: (int) $this->integer(self::CLINIC_ID),
            userId: (int) $this->user()?->id,
            startsAt: $this->string(self::STARTS_AT)->toString(),
            endsAt: $this->string(self::ENDS_AT)->toString(),
        );
    }
}
