<?php

namespace App\Console\Commands;

use App\Models\Investment;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InvestmentCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investment:completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the status completed for all investments that are completed';

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
        //Set the status completed for all investments whose investment_end_date is less than the current date
        try {
            $investments = Investment::where([
                ["status", "=", "inprogress"],
                ["investment_end_date", "<", date("Y-m-d H:i:s")]
            ])->get();

            if ($investments->count() == 0) {
                return;
            }

            $bar =
                $this->output->createProgressBar(count($investments));

            $bar->start();

            foreach ($investments as $investment) {
                $investment->update([
                    'status' => 'completed'
                ]);
                $bar->advance();
            }

            $bar->finish();

            return 0;

            $this->info('Investment Completed Finish');
        } catch (Exception $e) {
            Log::error("Investment Completed Command" . "===" .  $e->getMessage());
        }
    }
}
