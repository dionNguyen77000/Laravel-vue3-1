<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsMaterialSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_material_supplier', function (Blueprint $table) {
            $table->foreignId('goods_material_id')->constrained()->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onUpdate('cascade')
            ->onDelete('cascade');
            $table->float('unit_price')->default(0);
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
        Schema::dropIfExists('goods_material_supplier');
    }
}
