<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Doctors\App\Requests\UpsertDoctorRequest;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Actions\UpdateDoctorAction;
use Lightit\Doctors\Domain\Models\Doctor;

#[Group('Doctors')]
final readonly class UpdateDoctorController
{
    public function __invoke(
        Doctor $doctor,
        UpsertDoctorRequest $request,
        UpdateDoctorAction $updateDoctorAction,
    ): JsonResponse {
        $doctor = $updateDoctorAction->execute($doctor, $request->toDto());

        return DoctorResource::make($doctor)->response();
    }
}
