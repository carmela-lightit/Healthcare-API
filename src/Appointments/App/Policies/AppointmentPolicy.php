<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Policies;

use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Users\Domain\Models\User;

class AppointmentPolicy
{
    public function delete(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id;
    }
}
