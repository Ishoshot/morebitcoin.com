<?php

namespace App\Console\Commands;

use App\Models\Investment;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InvestmentWatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investment:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the Database for Outdated Investments based on the elapse_at column and sets their status to abadoned';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $investments = Investment::where([
                ["status", "=", "created"],
                ["elapse_at", "<=", date("Y-m-d H:i:s")]
            ])->get();

            if ($investments->count() == 0) {
                return;
            }

            $bar =
                $this->output->createProgressBar(count($investments));

            $bar->start();

            foreach ($investments as $investment) {
                $investment->update([
                    'status' => 'abandoned'
                ]);

                $bar->advance();
            }

            $bar->finish();

            $this->info('Investment Watch Finish');
        } catch (Exception $e) {
            Log::error("Investment Watch Command" . "===" .  $e->getMessage());
        }
    }
}
