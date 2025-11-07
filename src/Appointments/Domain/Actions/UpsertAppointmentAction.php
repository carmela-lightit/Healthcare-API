<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Models\Appointment;

final class UpsertAppointmentAction
{
    public function execute(AppointmentDto $dto, Appointment|null $appointment = null): Appointment
    {
        $appointment ??= new Appointment();

        $appointment->doctor_id = $dto->doctorId;
        $appointment->clinic_id = $dto->clinicId;
        $appointment->user_id = $dto->userId;
        $appointment->starts_at = $dto->startsAt;
        $appointment->ends_at = $dto->endsAt;

        $appointment->saveOrFail();

        $appointment->load(['doctor', 'clinic', 'user']);

        return $appointment;
    }
}
