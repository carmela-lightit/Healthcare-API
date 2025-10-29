<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Doctors\Domain\Models\Doctor;

#[Group('Doctors')]
final readonly class DeleteDoctorController
{
    public function __invoke(Doctor $doctor): JsonResponse
    {
        $doctor->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
