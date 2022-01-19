<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UserWithdrawalProcessed extends Notification
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
        Log::info("User Withdrawal Processed: Mail Sent to " . $this->user->profile->first_name . " " . $this->user->profile->last_name . " (" . $this->user->email . ")");

        $firstname = $this->user->profile->first_name;
        $withdrawal = $this->withdrawal;

        return (new MailMessage)
            ->subject("Your Withdrawal has been Processed 😃")
            ->markdown('mail.user.withdrawal.processed', ['firstname' => $firstname, 'withdrawal' => $withdrawal]);
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
