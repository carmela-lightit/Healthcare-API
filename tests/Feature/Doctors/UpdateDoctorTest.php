<?php

declare(strict_types=1);

namespace Tests\Feature\Doctors;

use Database\Factories\DoctorFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Controllers\UpdateDoctorController;
use Lightit\Doctors\Domain\Models\Doctor;
use Tests\RequestFactories\StoreDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

describe('doctors', function (): void {
    /** @see UpdateDoctorController */
    it('can update a doctor successfully', function (): void {
        $doctor = DoctorFactory::new()->createOne([
            'name' => 'Dr. Old Name',
        ]);

        $data = StoreDoctorRequestFactory::new()->create([
            'name' => 'Dr. Updated Name',
        ]);

        $response = putJson(url("/api/doctors/{$doctor->id}"), $data);

        $doctor = Doctor::query()->where('name', $data['name'])->firstOrFail();

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                    $json->has(
                        'data',
                        fn (AssertableJson $json): AssertableJson =>
                            $json
                                ->where('id', $doctor->id)
                                ->where('name', $doctor->name)
                                ->has('clinics')
                    )
            );

        assertDatabaseHas('doctors', ['name' => $data['name']]);
    });

    it('cannot update a doctor with invalid data', function (): void {
        $existingDoctor = DoctorFactory::new()->createOne();

        $data = ['name' => ''];

        $response = putJson(url("/api/doctors/{$existingDoctor->id}"), $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrorFor('name', 'error.fields');
    });
});
