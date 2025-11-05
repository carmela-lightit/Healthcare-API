<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\UserFactory;
use Lightit\Appointments\App\Controllers\ListMyAppointmentsController;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

describe('appointments', function (): void {
    /** @see ListMyAppointmentsController */
    it('lists authenticated user appointments', function (): void {
        $user = UserFactory::new()->createOne();
        [$doctor, $clinic] = makeDoctorAndClinic();
        makeAppointment($user, $doctor, $clinic);

        actingAs($user, 'api')
            ->getJson(url('/api/appointments/me'))
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data.0',
                    fn (AssertableJson $json) =>
                    $json->where('doctor.id', $doctor->id)
                        ->where('clinic.id', $clinic->id)
                        ->where('user.id', $user->id)
                        ->etc()
                )
            );
    });

    it('rejects unauthenticated users', function (): void {
        getJson(url('/api/appointments/me'))->assertUnauthorized();
    });
});
