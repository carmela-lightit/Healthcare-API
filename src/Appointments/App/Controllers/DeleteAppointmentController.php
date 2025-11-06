<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Requests\CancelAppointmentRequest;
use Lightit\Appointments\Domain\Models\Appointment;

#[Group('Appointments')]
final readonly class DeleteAppointmentController
{
    public function __invoke(CancelAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $appointment->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
