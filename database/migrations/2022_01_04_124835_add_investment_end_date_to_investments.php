<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvestmentEndDateToInvestments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dateTime('investment_end_date')->nullable();
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
            $table->dropColumn('investment_end_date');
        });
    }
}
