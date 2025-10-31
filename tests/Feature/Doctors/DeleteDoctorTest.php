<?php

declare(strict_types=1);

namespace Tests\Feature\Doctors;

use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Controllers\DeleteDoctorController;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

describe('doctors', function (): void {
    /** @see DeleteDoctorController */
    it('deletes a doctor and returns a successful response', function (): void {
        $existingDoctor = DoctorFactory::new()->createOne();
        $response = deleteJson("api/doctors/{$existingDoctor->id}");
        $response->assertNoContent();

        assertDatabaseMissing('doctors', ['id' => $existingDoctor->id]);
    });

    it('returns a 404 response when doctor is not found', function (): void {
        $nonExistentDoctorId = 99999;

        deleteJson("api/doctors/{$nonExistentDoctorId}")
            ->assertNotFound();
    });
});
