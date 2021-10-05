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
            $table->foreignId('orders_to_supplier_id')->nullable()->constrained()->onUpdate('cascade');
            $table->foreignId('invoices_from_supplier_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
            $table->string('goods_material')->nullable();
            $table->string('unit')->nullable(); 
            // $table->foreignId('goods_material_id')->nullable()->constrained()->onUpdate('no action')
            // ->onDelete('no action');
            $table->float('o_unit_quantity')->nullable();
            $table->float('o_unit_price')->nullable();
            $table->float('o_line_price')->nullable();
            $table->float('i_unit_quantity')->nullable();
            $table->float('i_unit_price')->nullable();
            $table->float('i_line_price')->nullable();
            $table->string('category');
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
