<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('plan');
            $table->string('status');
            $table->decimal('amountUSD', 10, 4);
            $table->decimal('amountBTC', 10, 8);
            $table->string('reference');
            $table->string('credit_reference')->nullable();
            $table->dateTime('elapse_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investments');
    }
}
