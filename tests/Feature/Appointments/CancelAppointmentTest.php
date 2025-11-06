<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\UserFactory;
use Lightit\Appointments\Domain\Models\Appointment;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

describe('appointments', function (): void {
    /** @see DeleteAppointmentController */
    it('soft deletes an appointment successfully', function (): void {
        $user = UserFactory::new()->createOne();
        $appointment = AppointmentFactory::new()->createOne([
            'user_id' => $user->id,
        ]);

        actingAs($user, 'api')
            ->deleteJson(url("/api/appointments/{$appointment->id}"))
            ->assertSuccessful();

        assertSoftDeleted(Appointment::class, ['id' => $appointment->id]);
    });

    it('rejects unauthenticated users', function (): void {
        $appointment = AppointmentFactory::new()->createOne();

        deleteJson(url("/api/appointments/{$appointment->id}"))->assertUnauthorized();
    });

    it('does not allow deleting appointments of other users', function (): void {
        $appointment = AppointmentFactory::new()->createOne();
        $otherUser = UserFactory::new()->createOne();

        actingAs($otherUser, 'api', function () use ($appointment): void {
            deleteJson(url("/api/appointments/{$appointment->id}"))
                ->assertForbidden();
        });

        assertDatabaseHas('appointments', ['id' => $appointment->id, 'deleted_at' => null]);
    });
});
