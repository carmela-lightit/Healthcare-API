<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lightit\Appointments\Domain\Models\Appointment;

final class CancelAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Appointment $appointment */
        $appointment = $this->route('appointment');
        $user = $this->user();

        return $user !== null && $user->can('delete', $appointment);
    }

    public function rules(): array
    {
        return [];
    }
}
