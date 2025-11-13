@component('mail::message')
# Appointment Confirmed

Hi {{ $user->name }},

Your appointment has been successfully scheduled.

@component('mail::panel')
**Doctor:** {{ $appointment->doctor->name }} <br>
**Clinic:** {{ $appointment->clinic->name }} <br>
**Date:** {{ $appointment->starts_at->format('F j, Y') }} <br>
**Time:** {{ $appointment->starts_at->format('H:i') }} - {{ $appointment->ends_at->format('H:i') }} <br>
@endcomponent


Thanks,
{{ config('app.name') }}
@endcomponent
