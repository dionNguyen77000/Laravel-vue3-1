<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('goods_material_id')->constrained()->onUpdate('cascade') ->onDelete('cascade');
            $table->foreignId('intermediate_product_id')->constrained()->onUpdate('cascade') ->onDelete('cascade');
            $table->float('rate')->nullable()->default(1);
            $table->string('type')->nullable();
            $table->string('category')->nullable();
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
        Schema::dropIfExists('unit__conversations');
    }
}
