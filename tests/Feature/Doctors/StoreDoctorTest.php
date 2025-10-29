<?php

declare(strict_types=1);

namespace Tests\Feature\Doctors;

use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Controllers\StoreDoctorController;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Models\Doctor;
use Tests\RequestFactories\StoreDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

dataset(name: 'validation-rules', dataset: [
    'name is required' => ['name', ''],
    'name must be string' => ['name', ['array']],
    'name not too short' => ['name', 'Dr'],
    'name not too long' => ['name', str_repeat('a', 101)],
]);

describe('doctors', function (): void {
    /** @see StoreDoctorController */
    it('can create a doctor successfully', function (): void {
        $data = StoreDoctorRequestFactory::new()->create([
            'name' => 'Dr. Watson',
        ]);

        $response = postJson(url('/api/doctors'), $data);

        $doctor = Doctor::query()->where('name', $data['name'])->firstOrFail();

        $response
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson =>
                $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json->whereAll(
                        DoctorResource::make($doctor)->resolve()
                    )
                )
            );

        assertDatabaseHas('doctors', ['name' => $data['name']]);
    });

    it('cannot create a doctor with invalid data', function (string $field, string|array $value): void {
        $data = StoreDoctorRequestFactory::new()->create();

        $response = postJson(url('/api/doctors'), [...$data, $field => $value]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([$field], 'error.fields');
    })->with('validation-rules');
});
