<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Date;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Rules\DoctorClinicAssignmentRule;
use Lightit\Appointments\Domain\Rules\NoDoctorAppointmentOverlapRule;
use Lightit\Appointments\Domain\Rules\NoUserAppointmentOverlapRule;
use Lightit\Clinics\Domain\Rules\ValidClinicIds;
use Lightit\Doctors\Domain\Rules\ValidDoctorIds;
use Lightit\Users\Domain\Models\User;

final class UpsertAppointmentRequest extends FormRequest
{
    public const string DOCTOR_ID = 'doctor_id';

    public const string CLINIC_ID = 'clinic_id';

    public const string STARTS_AT = 'starts_at';

    public const string ENDS_AT = 'ends_at';

    /**
     * @return array<string, list<string|ValidationRule|Date>>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();
        $doctorId = (int) $this->integer(self::DOCTOR_ID);
        $clinicId = (int) $this->integer(self::CLINIC_ID);
        $startsAt = CarbonImmutable::parse($this->string(self::STARTS_AT)->toString());
        $endsAt = CarbonImmutable::parse($this->string(self::ENDS_AT)->toString());

        return [
            self::DOCTOR_ID => ['required', 'integer',
                new ValidDoctorIds(),
                new DoctorClinicAssignmentRule($doctorId, $clinicId)],
            self::CLINIC_ID => ['required', 'integer', new ValidClinicIds()],
            self::STARTS_AT => ['required',
                Rule::date()->afterOrEqual('now'),
                new NoDoctorAppointmentOverlapRule($doctorId, $startsAt, $endsAt),
                new NoUserAppointmentOverlapRule($user->id, $startsAt, $endsAt)],
            self::ENDS_AT => ['required', Rule::date()->after(self::STARTS_AT)],
        ];
    }

    public function toDto(): AppointmentDto
    {
        return new AppointmentDto(
            doctorId: (int) $this->integer(self::DOCTOR_ID),
            clinicId: (int) $this->integer(self::CLINIC_ID),
            userId: (int) $this->user()?->id,
            startsAt: CarbonImmutable::parse($this->string(self::STARTS_AT)->toString()),
            endsAt: CarbonImmutable::parse($this->string(self::ENDS_AT)->toString()),
        );
    }
}
