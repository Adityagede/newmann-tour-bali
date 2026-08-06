<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CustomTripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class NewCustomTripRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly CustomTripRequest $customTripRequest
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicle = $this->customTripRequest->selected_vehicle;

        if (
            $vehicle === 'Another Car'
            && $this->customTripRequest->custom_vehicle
        ) {
            $vehicle = $this->customTripRequest->custom_vehicle;
        }

        return (new MailMessage)
            ->subject(
                'New Custom Trip Request — '
                . $this->customTripRequest->booking_code
            )
            ->greeting('New custom trip request received')
            ->line(
                'A guest submitted a custom Bali trip request '
                . 'through the Newman website.'
            )
            ->line(
                'Reference: '
                . $this->customTripRequest->booking_code
            )
            ->line(
                'Guest Name: '
                . $this->customTripRequest->name
            )
            ->line(
                'WhatsApp: '
                . $this->customTripRequest->whatsapp
            )
            ->line(
                'Email: '
                . ($this->customTripRequest->email ?: '-')
            )
            ->line(
                'Trip Date: '
                . ($this->customTripRequest->trip_date?->format('d M Y') ?: '-')
            )
            ->line(
                'Number of Guests: '
                . ($this->customTripRequest->people_count ?: '-')
            )
            ->line('Vehicle: ' . ($vehicle ?: '-'))
            ->line(
                'Pickup Area: '
                . ($this->customTripRequest->pickup_area ?: '-')
            )
            ->line(
                'Trip Plan: '
                . ($this->customTripRequest->message ?: '-')
            )
            ->action(
                'View Custom Trip Request',
                route(
                    'admin.custom-trip-requests.show',
                    $this->customTripRequest
                )
            )
            ->line(
                'This request is separate from the Tour Package V2 '
                . 'booking flow and is currently Pending.'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
