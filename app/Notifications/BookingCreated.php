<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification
{
    use Queueable;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Booking Created')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new booking has been made at your hotel:')
            ->line('Hotel: ' . ($this->booking->room->hotel->name ?? 'N/A'))
            ->line('Room: ' . ($this->booking->room->room_type ?? 'N/A'))
            ->line('Check-in: ' . $this->booking->check_in)
            ->line('Check-out: ' . $this->booking->check_out)
            ->line('Total Price: $' . $this->booking->total_price)
            ->line('Booking ID: ' . $this->booking->booking_id)
            ->action('View Booking', url('/dashboard/bookings/' . $this->booking->id));
    }

    public function toDatabase($notifiable)
    {
        // Customize the data that will be stored in the database
        return [
            'message' => 'A new booking has been created for your hotel.',
            'booking_id' => $this->booking->booking_id,
            'hotel_name' => $this->booking->room->hotel->name,
            'check_in' => $this->booking->check_in,
            'check_out' => $this->booking->check_out,
        ];
    }
}
