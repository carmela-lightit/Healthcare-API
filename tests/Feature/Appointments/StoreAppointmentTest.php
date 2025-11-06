<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Date;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Appointments\App\Controllers\StoreAppointmentController;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

describe('appointments', function (): void {
    /** @see StoreAppointmentController */
    it('creates an appointment successfully', function (): void {
        $doctor = DoctorFactory::new()->has(ClinicFactory::new())->createOne();
        $clinicId = $doctor->clinics()->firstOrFail()->id;
        $user = UserFactory::new()->createOne();

        $data = [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'starts_at' => Date::now()->addDay()->setHour(10)->toIso8601String(),
            'ends_at' => Date::now()->addDay()->setHour(11)->toIso8601String(),
        ];

        $response = actingAs($user, 'api')->postJson(url('/api/appointments'), $data);

        $response->assertCreated()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson =>
                    $json->where('doctor.id', $doctor->id)
                         ->where('clinic.id', $clinicId)
                         ->where('user.id', $user->id)
                         ->etc()
                )
            );

        assertDatabaseHas('appointments', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'user_id' => $user->id,
        ]);
    });

    it('rejects unauthenticated users', function (): void {
        $doctor = DoctorFactory::new()->has(ClinicFactory::new())->createOne();
        $clinicId = $doctor->clinics()->firstOrFail()->id;
        $data = [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'starts_at' => Date::now()->addDay(),
            'ends_at' => Date::now()->addDay()->addHour(),
        ];

        postJson(url('/api/appointments'), $data)->assertUnauthorized();
    });

    it('rejects invalid times or overlaps', function (): void {
        $doctor = DoctorFactory::new()->has(ClinicFactory::new())->createOne();
        $clinicId = $doctor->clinics()->firstOrFail()->id;
        $user = UserFactory::new()->createOne();
        $pastData = [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'starts_at' => Date::now()->subHour()->toIso8601String(),
            'ends_at' => Date::now()->addHour()->toIso8601String(),
        ];
        actingAs($user, 'api')->postJson(url('/api/appointments'), $pastData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['starts_at'], 'error.fields');

        AppointmentFactory::new()->createOne([
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'user_id' => $user->id,
            'starts_at' => Date::now()->addDay()->setHour(9),
            'ends_at' => Date::now()->addDay()->setHour(10),
        ]);
        $overlapData = [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'starts_at' => Date::now()->addDay()->setHour(9)->toIso8601String(),
            'ends_at' => Date::now()->addDay()->setHour(10)->toIso8601String(),
        ];
        actingAs($user, 'api')->postJson(url('/api/appointments'), $overlapData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['starts_at'], 'error.fields');
    });

    it('rejects if doctor is not assigned to clinic', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        $user = UserFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $data = [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'starts_at' => Date::now()->addDay()->setHour(11)->toIso8601String(),
            'ends_at' => Date::now()->addDay()->setHour(12)->toIso8601String(),
        ];

        actingAs($user, 'api')->postJson(url('/api/appointments'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['doctor_id'], 'error.fields');
    });

    it('rejects if ends_at is before starts_at', function (): void {
        $doctor = DoctorFactory::new()->has(ClinicFactory::new())->createOne();
        $clinicId = $doctor->clinics()->firstOrFail()->id;
        $user = UserFactory::new()->createOne();
        $data = [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinicId,
            'starts_at' => Date::now()->addDay()->setHour(12)->toIso8601String(),
            'ends_at' => Date::now()->addDay()->setHour(11)->toIso8601String(),
        ];

        actingAs($user, 'api')->postJson(url('/api/appointments'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ends_at'], 'error.fields');
    });
});
