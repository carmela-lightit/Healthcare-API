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
        $clinicFactory = ClinicFactory::new();
        $doctorFactory = DoctorFactory::new()
            ->has($clinicFactory);
        $startsAt = CarbonImmutable::now()->addDay()->startOfHour();
        $endsAt = $startsAt->addHour();

        return [
            'user_id' => UserFactory::new(),
            'doctor_id' => $doctorFactory,
            'clinic_id' => $clinicFactory,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }
}
