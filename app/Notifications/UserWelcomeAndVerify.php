<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserWelcomeAndVerify extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        $firstname = $this->user->profile->first_name;
        $code = $this->user->verification_code;

        return (new MailMessage)->subject("We're thrilled to have you at MoreBitcoin 😃")->markdown('mail.user.welcome', ['firstname' => $firstname, 'code' => $code]);
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
