<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\CarbonImmutable;
use Lightit\Appointments\Domain\Models\Appointment;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $user = UserFactory::new()->createOne();
        $doctor = DoctorFactory::new()
            ->has(ClinicFactory::new())
            ->createOne();

        $startsAt = CarbonImmutable::now()->addDay()->startOfHour();
        $endsAt = $startsAt->addHour();

        return [
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $doctor->clinics()->firstOrFail()->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }
}
