<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Models\Appointment;

#[Group('Appointments')]
final readonly class ListMyAppointmentsController
{
    public function __invoke(): AnonymousResourceCollection
    {
        $appointments = Appointment::with(['doctor', 'clinic', 'user'])
            ->where('user_id', Auth::id())
            ->get();

        return AppointmentResource::collection($appointments);
    }
}
