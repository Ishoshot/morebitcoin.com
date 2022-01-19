<?php

namespace App\Jobs\Admin;

use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\AdminWithdrawalInitiated as NotificationsAdminWithdrawalInitiated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class AdminWithdrawalInitiated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $withdrawal;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Withdrawal $withdrawal)
    {
        $this->user = $user;
        $this->withdrawal = $withdrawal;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $admin =  User::where('role_id', 2)->first();
        if ($admin) {
            Notification::send($admin, new NotificationsAdminWithdrawalInitiated($this->user, $this->withdrawal));
        }
    }
}
