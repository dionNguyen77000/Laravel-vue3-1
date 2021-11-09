<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_journey', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('journey');
            $table->time('depart');
            $table->time('estimated_return');
            $table->time('return')->nullable();
            $table->integer('estimated_duration');
            $table->string('status');
            $table->string('Payment')->nullable();
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
        Schema::dropIfExists('delivery_journey');
    }
}
