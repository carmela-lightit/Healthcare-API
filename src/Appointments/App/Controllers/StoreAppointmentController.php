<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Requests\UpsertAppointmentRequest;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\UpsertAppointmentAction;
use Lightit\Users\Domain\Models\User;

#[Group('Appointments')]
final readonly class StoreAppointmentController
{
    public function __invoke(
        UpsertAppointmentRequest $request,
        UpsertAppointmentAction $action,
        #[CurrentUser]
        User $user,
    ): JsonResponse {
        $appointment = $action->execute($request->toDto($user));

        return AppointmentResource::make($appointment)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
