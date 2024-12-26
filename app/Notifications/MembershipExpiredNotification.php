<?php

namespace App\Notifications;

use App\Mail\MembershipExpiredMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\SentMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class MembershipExpiredNotification extends Notification
{
    use Queueable;

    private $membership;

    /**
     * Create a new notification instance.
     */
    public function __construct($membership)
    {
        $this->membership = $membership;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): SentMessage|null
    {
        return Mail::to($notifiable->email)
            ->send(new MembershipExpiredMail($this->membership));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
