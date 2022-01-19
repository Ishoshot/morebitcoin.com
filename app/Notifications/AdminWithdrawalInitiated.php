<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AdminWithdrawalInitiated extends Notification
{
    use Queueable;

    public $user;
    public $withdrawal;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Withdrawal $withdrawal)
    {
        $this->user = $user;
        $this->withdrawal = $withdrawal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        Log::info("Admin Withdrawal Initiated: Mail Sent to Admin");

        $user = $this->user;
        $withdrawal = $this->withdrawal;

        return (new MailMessage)
            ->subject("Incoming Withdrawal Request")
            ->markdown('mail.admin.withdrawal.initiated', ['user' => $user, 'withdrawal' => $withdrawal]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
