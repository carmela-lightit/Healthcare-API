<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Clinics\Domain\Models\Clinic;

#[Group('Clinics')]
final readonly class DeleteClinicController
{
    public function __invoke(Clinic $clinic): JsonResponse
    {
        $clinic->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
