<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\ListMyAppointmentsAction;

#[Group('Appointments')]
final readonly class ListMyAppointmentsController
{
    public function __invoke(ListMyAppointmentsAction $action): JsonResponse
    {
        $appointments = $action->execute();

        return AppointmentResource::collection($appointments)
            ->response();
    }
}
