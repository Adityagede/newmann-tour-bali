<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\TourBookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class NewTourBookingRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly TourBookingRequest $booking
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tourTitle = $this->booking->tourPackage?->title
            ?? data_get(
                $this->booking->tour_snapshot,
                'title',
                'Unavailable tour'
            );

        $optionTitle = $this->booking->tourOption?->title
            ?? data_get(
                $this->booking->option_snapshot,
                'title',
                'Unavailable option'
            );

        $travelDate = $this->booking->travel_date?->format(
            'd M Y'
        ) ?? '-';

        $startingTime = substr(
            (string) $this->booking->starting_time,
            0,
            5
        );

        return (new MailMessage)
            ->subject(
                'New Tour Booking Request — '
                . $this->booking->booking_reference
            )
            ->greeting('New Tour Booking Request V2 received')
            ->line(
                'A new request has been submitted through the '
                . 'Tour Package V2 booking flow.'
            )
            ->line(
                'Booking Reference: '
                . $this->booking->booking_reference
            )
            ->line(
                'Guest Name: '
                . $this->booking->guest_name
            )
            ->line(
                'WhatsApp: '
                . $this->booking->guest_whatsapp
            )
            ->line(
                'Email: '
                . ($this->booking->guest_email ?: '-')
            )
            ->line('Tour: ' . $tourTitle)
            ->line('Option: ' . $optionTitle)
            ->line('Travel Date: ' . $travelDate)
            ->line(
                'Starting Time: '
                . ($startingTime !== '' ? $startingTime : '-')
            )
            ->line(
                'Language: '
                . $this->booking->language
            )
            ->line(
                'Participants: '
                . $this->booking->total_participants
                . ' ('
                . $this->booking->adult_count
                . ' Adult, '
                . $this->booking->child_count
                . ' Child, '
                . $this->booking->infant_count
                . ' Infant)'
            )
            ->line(
                'Estimated Total: '
                . $this->formatMoney(
                    $this->booking->estimated_total,
                    $this->booking->currency
                )
            )
            ->line(
                'Pickup Address: '
                . $this->booking->pickup_address
            )
            ->line(
                'Special Requests: '
                . ($this->booking->special_requests ?: '-')
            )
            ->action(
                'View V2 Booking Detail',
                route(
                    'admin.tour-booking-requests.show',
                    $this->booking
                )
            )
            ->line(
                'The request is currently Pending. '
                . 'Please review it in the Newman Admin dashboard.'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    private function formatMoney(
        ?int $amount,
        string $currency
    ): string {
        if ($amount === null) {
            return '-';
        }

        return strtoupper($currency)
            . ' '
            . number_format(
                $amount,
                0,
                ',',
                '.'
            );
    }
}
