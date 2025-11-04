<?php

declare(strict_types=1);

namespace Tests\Feature\Doctors;

use Database\Factories\DoctorFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Controllers\GetDoctorController;
use function Pest\Laravel\getJson;

describe('doctors', function (): void {
    /** @see GetDoctorController */
    it('retrieves a doctor and returns a successful response', function (): void {
        $existingDoctor = DoctorFactory::new()->createOne();

        getJson("api/doctors/{$existingDoctor->id}")
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                    $json->has(
                        'data',
                        fn (AssertableJson $json): AssertableJson =>
                            $json
                                ->where('id', $existingDoctor->id)
                                ->where('name', $existingDoctor->name)
                                ->has('clinics')
                    )
            );
    });

    it('returns a 404 response when doctor is not found', function (): void {
        $nonExistentDoctorId = 99999;

        getJson("api/doctors/{$nonExistentDoctorId}")
            ->assertNotFound();
    });
});
