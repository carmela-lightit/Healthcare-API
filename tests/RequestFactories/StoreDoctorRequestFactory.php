<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

final class StoreDoctorRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => 'Dr. Example',
        ];
    }
}
