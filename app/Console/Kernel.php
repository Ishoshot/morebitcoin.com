<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('otp:clean')->daily();
        $schedule->command('investment:watch')->everyFifteenMinutes()->emailOutputTo('ishoshot@gmail.com');
        $schedule->command('investment:completed')->everyThirtyMinutes()->emailOutputTo('ishoshot@gmail.com');
        $schedule->command('investment:payout')->dailyAt('00:00')->emailOutputTo('ishoshot@gmail.com');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
