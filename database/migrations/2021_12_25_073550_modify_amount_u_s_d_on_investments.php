<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAmountUSDOnInvestments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investments', function (Blueprint $table) {
            //modify amount
            $table->decimal('amountUSD', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investments', function (Blueprint $table) {
            //revert changes
            $table->decimal('amountUSD', 10, 4)->change();
        });
    }
}
