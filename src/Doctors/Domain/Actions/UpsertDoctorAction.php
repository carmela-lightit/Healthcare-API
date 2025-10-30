<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class UpsertDoctorAction
{
    public function execute(DoctorDto $doctorDto, Doctor|null $doctor = null): Doctor
    {
        if (! $doctor instanceof Doctor) {
            $doctor = new Doctor();
        }

        $doctor->name = $doctorDto->name;

        $doctor->saveOrFail();

        return $doctor;
    }
}
