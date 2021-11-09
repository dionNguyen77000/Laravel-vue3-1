<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('date');
            $table->string('driver');
            $table->string('order_number');
            $table->string('suburb');
            $table->string('zone');
            $table->string('journey');
            $table->time('depart')->nullable();;
            $table->integer('estimated_duration')->nullable();;
            $table->time('estimated_return')->nullable();;
            $table->time('return')->nullable();
            $table->string('Fuel_Cost')->nullable();
            $table->string('Cash_Recieved')->nullable();
            $table->string('Change')->nullable();
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
