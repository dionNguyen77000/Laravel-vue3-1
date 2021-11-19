<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryTravelTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_travel_times', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('destination_one_id')->unsigned()->index()->nullable();
            $table->bigInteger('destination_two_id')->unsigned()->index()->nullable();
            $table->foreign('destination_one_id')->references('id')->on('delivery_settings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('destination_two_id')->references('id')->on('delivery_settings')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('estimated_duration')->nullable()->default(0);
            $table->unique(['destination_one_id', 'destination_two_id']);

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
        Schema::dropIfExists('delivery_travel_time');
    }
}
