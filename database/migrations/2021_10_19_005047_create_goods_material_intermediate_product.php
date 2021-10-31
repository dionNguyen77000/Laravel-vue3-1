<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsMaterialIntermediateProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_material_intermediate_product', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('goods_material_id')->unsigned()->nullable();
            $table->bigInteger('intermediate_product_id')->unsigned()->nullable();
            $table->foreign('goods_material_id','g_i_foreign')->references('id')
            ->on('goods_materials')->onUpdate('cascade');
            $table->foreign('intermediate_product_id','i_g_foreign')->references('id')
            ->on('intermediate_products')->onUpdate('cascade')->onDelete('cascade');
            $table->string('amount')->nullable();
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
        Schema::dropIfExists('goods_material_intermediate_product');
    }
}
