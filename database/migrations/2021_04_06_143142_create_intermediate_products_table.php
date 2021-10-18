<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntermediateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intermediate_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->float('price')->default(0);
            // $table->unsignedBigInteger('unit_id');
            // $table->foreign('unit_id')->references('id')->on('units');
            $table->foreignId('category_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
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
        Schema::dropIfExists('intermediate_products');
    }
}
