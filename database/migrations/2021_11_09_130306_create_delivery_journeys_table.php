<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_journeys', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('journey');
            $table->time('departure');
            $table->integer('estimated_duration');
            $table->time('estimated_return');
            $table->time('actual_return')->nullable();
            $table->string('status');
            $table->float('fuel_payment')->nullable();
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
