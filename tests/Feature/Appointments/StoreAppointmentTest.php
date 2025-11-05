<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\UserFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Appointments\App\Controllers\StoreAppointmentController;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $this->user = UserFactory::new()->createOne();
    $this->doctor = DoctorFactory::new()->createOne();
    $this->clinic = ClinicFactory::new()->createOne();
    $this->doctor->clinics()->attach($this->clinic->id);
});

describe('appointments', function (): void {
    /** @see StoreAppointmentController */
    it('creates an appointment successfully', function (): void {
        $data = [
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'starts_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(10)->toIso8601String(),
            'ends_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(11)->toIso8601String(),
        ];

        $response = actingAs($this->user, 'api')->postJson(url('/api/appointments'), $data);

        $response->assertCreated()
            ->assertJson(
                fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson =>
                $json->has(
                    'data',
                    fn (AssertableJson $json): \Illuminate\Testing\Fluent\AssertableJson =>
                    $json->where('doctor.id', $this->doctor->id)
                         ->where('clinic.id', $this->clinic->id)
                         ->where('user.id', $this->user->id)
                         ->etc()
                )
            );

        assertDatabaseHas('appointments', [
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);
    });

    it('rejects unauthenticated users', function (): void {
        $data = [
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'starts_at' => \Illuminate\Support\Facades\Date::now()->addDay(),
            'ends_at' => \Illuminate\Support\Facades\Date::now()->addDay()->addHour(),
        ];

        postJson(url('/api/appointments'), $data)->assertUnauthorized();
    });

    it('rejects invalid times or overlaps', function (): void {
        $pastData = [
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'starts_at' => \Illuminate\Support\Facades\Date::now()->subHour()->toIso8601String(),
            'ends_at' => \Illuminate\Support\Facades\Date::now()->addHour()->toIso8601String(),
        ];
        actingAs($this->user, 'api')->postJson(url('/api/appointments'), $pastData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['starts_at'], 'error.fields');

        AppointmentFactory::new()->createOne([
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'starts_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(9),
            'ends_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(10),
        ]);
        $overlapData = [
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'starts_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(9)->toIso8601String(),
            'ends_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(10)->toIso8601String(),
        ];
        actingAs($this->user, 'api')->postJson(url('/api/appointments'), $overlapData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['starts_at'], 'error.fields');
    });

    it('rejects if doctor is not assigned to clinic', function (): void {
        $clinic2 = ClinicFactory::new()->createOne();
        $data = [
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $clinic2->id,
            'starts_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(11)->toIso8601String(),
            'ends_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(12)->toIso8601String(),
        ];

        actingAs($this->user, 'api')->postJson(url('/api/appointments'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['doctor_id'], 'error.fields');
    });

    it('rejects if ends_at is before starts_at', function (): void {
        $data = [
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'starts_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(12)->toIso8601String(),
            'ends_at' => \Illuminate\Support\Facades\Date::now()->addDay()->setHour(11)->toIso8601String(),
        ];

        actingAs($this->user, 'api')->postJson(url('/api/appointments'), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ends_at'], 'error.fields');
    });
});
