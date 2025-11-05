<?php

declare(strict_types=1);

namespace Tests\Feature\Appointments;

use Database\Factories\AppointmentFactory;
use Database\Factories\UserFactory;
use Lightit\Appointments\App\Controllers\DeleteAppointmentController;
use Lightit\Appointments\Domain\Models\Appointment;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

beforeEach(function (): void {
    $this->user = UserFactory::new()->createOne();

    $this->appointment = AppointmentFactory::new()->createOne([
        'user_id' => $this->user->id,
    ]);
});

describe('appointments', function (): void {
    /** @see DeleteAppointmentController */
    it('soft deletes an appointment successfully', function (): void {
        actingAs($this->user, 'api')
            ->deleteJson(url("/api/appointments/{$this->appointment->id}"))
            ->assertSuccessful();

        assertSoftDeleted(Appointment::class, ['id' => $this->appointment->id]);
    });

    it('rejects unauthenticated users', function (): void {
        deleteJson(url("/api/appointments/{$this->appointment->id}"))->assertUnauthorized();
    });

    it('does not allow deleting appointments of other users', function (): void {
        $otherUser = UserFactory::new()->createOne();

        actingAs($otherUser, 'api', function (): void {
            deleteJson(url("/api/appointments/{$this->appointment->id}"))
                ->assertForbidden();
        });

        assertDatabaseHas('appointments', ['id' => $this->appointment->id, 'deleted_at' => null]);
    });
});
