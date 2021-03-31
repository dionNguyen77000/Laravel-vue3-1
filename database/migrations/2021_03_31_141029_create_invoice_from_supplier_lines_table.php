<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceFromSupplierLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_from_supplier_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoices_from_supplier_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('invoice_from_supplier_lines');
    }
}
