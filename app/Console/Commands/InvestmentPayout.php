<?php

namespace App\Console\Commands;

use App\Models\Investment;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InvestmentPayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investment:payout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Checks the Database for Ongoing Investments, and Update the user's available_balance with the appropriate amount based on investment plan";

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
                ["status", "=", "inprogress"],
                ["investment_end_date", ">=", date("Y-m-d H:i:s")]
            ])->get();

            if ($investments->count() == 0) {
                return;
            }

            $bar =
                $this->output->createProgressBar(count($investments));

            $bar->start();

            foreach ($investments as $investment) {
                $amount = 0;
                if ($investment->plan == 'Hydrogen') {
                    $amount = 2.5;
                } else if ($investment->plan == 'Helium') {
                    $amount = 71.4;
                } else if ($investment->plan == 'Lithium') {
                    $amount = 714.28;
                } else {
                    $amount = 2857.14;
                }

                $investment->user->profile->update([
                    'available_balance' => $investment->user->profile->available_balance + $amount
                ]);

                $bar->advance();
            }

            $bar->finish();

            $this->info('Investment Payout Finish');
        } catch (Exception $e) {
            Log::error("Investment Payout Command" . "===" .  $e->getMessage());
        }
    }
}
