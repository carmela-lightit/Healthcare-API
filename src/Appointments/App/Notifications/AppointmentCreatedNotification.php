<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Lightit\Appointments\Domain\Models\Appointment;

final class AppointmentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return new MailMessage()
            ->subject('Appointment Scheduled')
            ->markdown('mails.appointments.created', [
                'appointment' => $this->appointment,
                'user' => $notifiable,
            ]);
    }
}
