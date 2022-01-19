<?php

namespace App\Jobs\Admin;

use App\Models\Investment;
use App\Models\User;
use App\Notifications\AdminInvestmentInprogress as AdminInvestmentInprogressNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class AdminInvestmentInprogress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $investment;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Investment $investment)
    {
        $this->user = $user;
        $this->investment = $investment;
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
            Notification::send($admin, new AdminInvestmentInprogressNotification($this->user, $this->investment));
        }
    }
}