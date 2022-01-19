<?php

namespace App\Notifications;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UserInvestmentInprogress extends Notification
{
    use Queueable;

    public $user;
    public $investment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Investment $investment)
    {
        $this->user = $user;
        $this->investment = $investment;
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

        Log::info("User Investment Inprogress: Mail Sent to " . $this->user->profile->first_name . " " . $this->user->profile->last_name . " (" . $this->user->email . ")");

        $firstname = $this->user->profile->first_name;
        $investment = $this->investment;


        $data = [];
        if ($investment->plan == "Hydrogen") {
            $data = [
                "duration" => "84 days",
                "first" => "$10 BTC - 7 days",
                "second" => "$40 BTC - 28 days",
                "third" => "$100 BTC - 84 days",
            ];
        } else if ($investment->plan == "Helium") {
            $data = [
                "duration" => "84 days",
                "first" => "$500 BTC - 7 days",
                "second" => "$600 BTC - 28 days",
                "third" => "$2,000 BTC - 84 days",
            ];
        } else if ($investment->plan == "Lithium") {
            $data = [
                "duration" => "35 days",
                "first" => "$714.28 BTC - Daily",
                "second" => "$5,000 BTC - 7 days",
                "third" => "$25,000 BTC - 35 days",
            ];
        } else {
            $data = [
                "duration" => "35 days",
                "first" => "$2,857.14 BTC - Daily",
                "second" => "$20,000 BTC - 7 days",
                "third" => "$100,000 BTC - 35 days",
            ];
        }

        return (new MailMessage)
            ->subject("You Investment has been Confirmed on MoreBitcoin 😃")
            ->markdown('mail.user.investment.inprogress', ['firstname' => $firstname, 'investment' => $investment, 'data' => $data]);
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
