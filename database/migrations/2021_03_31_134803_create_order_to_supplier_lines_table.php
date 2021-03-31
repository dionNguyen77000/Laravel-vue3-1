<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderToSupplierLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_to_supplier_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orders_to_supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('goods_material_id')->constrained();
            $table->string('unit_quantity');
            $table->integer('line_price');
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
        Schema::dropIfExists('order_to_supplier_lines');
    }
}
