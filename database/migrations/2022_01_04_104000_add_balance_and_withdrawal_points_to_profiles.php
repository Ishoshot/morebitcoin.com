<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceAndWithdrawalPointsToProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('withdrawal_point')->default(0)->nullable();
            $table->decimal('available_balance', 10, 2)->default(0.00)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('withdrawal_point');
            $table->dropColumn('available_balance');
        });
    }
}
