<?php

declare(strict_types=1);

namespace Tests\Feature\Doctors;

use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Controllers\ListDoctorController;
use function Pest\Laravel\getJson;

describe('doctors', function (): void {
    /** @see ListDoctorController */
    it('can list doctors successfully', function (): void {
        $doctors = DoctorFactory::new()->createMany(5);

        getJson(url('/api/doctors'))
            ->assertSuccessful()
            ->assertJsonCount(5, 'data');
    });
});
