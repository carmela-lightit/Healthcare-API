<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\UserFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Appointments\App\Controllers\ListMyAppointmentsController;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

describe('appointments', function (): void {
    /** @see ListMyAppointmentsController */
    it('lists authenticated user appointments', function (): void {
        $doctor = DoctorFactory::new()->has(ClinicFactory::new())->createOne();
        $clinicId = $doctor->clinics()->firstOrFail()->id;
        $user = UserFactory::new()->createOne();

        AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'user_id' => $user->id,
        ]);

        actingAs($user, 'api')
            ->getJson(url('/api/appointments/me'))
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                $json->has(
                    'data.0',
                    fn (AssertableJson $json): AssertableJson =>
                    $json->where('doctor.id', $doctor->id)
                        ->where('clinic.id', $clinicId)
                        ->where('user.id', $user->id)
                        ->etc()
                )->etc()
            );
    });

    it('rejects unauthenticated users', function (): void {
        getJson(url('/api/appointments/me'))->assertUnauthorized();
    });
});
