<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\UserFactory;
use Lightit\Appointments\App\Controllers\ListMyAppointmentsController;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function (): void {
    $this->user = UserFactory::new()->createOne();
    $this->doctor = DoctorFactory::new()->createOne();
    $this->clinic = ClinicFactory::new()->createOne();
    $this->doctor->clinics()->attach($this->clinic->id);

    $this->appointment = AppointmentFactory::new()->createOne([
        'user_id' => $this->user->id,
        'doctor_id' => $this->doctor->id,
        'clinic_id' => $this->clinic->id,
    ]);
});

describe('appointments', function (): void {
    /** @see ListMyAppointmentsController */
    it('lists authenticated user appointments', function (): void {
        actingAs($this->user, 'api')
            ->getJson(url('/api/appointments/me'))
            ->assertOk()
            ->assertJson(
                fn ($json) =>
                $json->has(
                    'data.0',
                    fn ($json) =>
                    $json->where('doctor.id', $this->doctor->id)
                        ->where('clinic.id', $this->clinic->id)
                        ->where('user.id', $this->user->id)
                        ->etc()
                )
            );
    });

    it('rejects unauthenticated users', function (): void {
        getJson(url('/api/appointments/me'))->assertUnauthorized();
    });
});
