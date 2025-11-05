<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\Domain\Actions\CancelAppointmentAction;
use Lightit\Appointments\Domain\Models\Appointment;

#[Group('Appointments')]
final readonly class DeleteAppointmentController
{
    use AuthorizesRequests;

    public function __invoke(Appointment $appointment, CancelAppointmentAction $action): JsonResponse
    {
        $this->authorize('delete', $appointment);

        $action->execute($appointment);

        return response()->json([
            'message' => 'Appointment cancelled successfully.',
        ], JsonResponse::HTTP_OK);
    }
}
