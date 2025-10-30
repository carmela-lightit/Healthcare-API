<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Lightit\Clinics\Domain\DataTransferObjects\ClinicDto;
use Lightit\Clinics\Domain\Models\Clinic;

class UpsertClinicAction
{
    public function execute(ClinicDto $clinicDto, Clinic|null $clinic = null): Clinic
    {
        if (! $clinic instanceof Clinic) {
            $clinic = new Clinic();
        }

        $clinic->name = $clinicDto->name;
        $clinic->address = $clinicDto->address;

        $clinic->saveOrFail();

        if ($clinicDto->doctorIds !== []) {
            $clinic->doctors()->attach($clinicDto->doctorIds);
        }

        $clinic->loadCount('doctors');

        return $clinic;
    }
}
