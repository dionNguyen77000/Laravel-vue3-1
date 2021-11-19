<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('delivery_journey_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('driver');
            $table->string('order_number');
            $table->string('suburb');
            $table->string('zone');
            $table->float('cash_recieved')->nullable();
            $table->float('change')->nullable();
            $table->string('journey')->nullable();
            $table->time('departure')->nullable();;
            $table->integer('estimated_duration_arrival')->nullable();;
            $table->time('estimated_arrival')->nullable();;
            $table->time('attual_arrival')->nullable();;
            $table->integer('estimated_duration_return')->nullable();;
            $table->time('estimated_return')->nullable();;
            $table->time('actual_return')->nullable();
            $table->float('fuel_cost')->nullable();
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
        Schema::dropIfExists('delivery_detail');
    }
}
