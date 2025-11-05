<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
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
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $doctor->clinics()->attach($clinic->id);

        $startsAt = Carbon::now()->addDay()->startOfHour();
        $endsAt = Carbon::now()->addDay()->startOfHour()->addHour();

        return [
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }
}
