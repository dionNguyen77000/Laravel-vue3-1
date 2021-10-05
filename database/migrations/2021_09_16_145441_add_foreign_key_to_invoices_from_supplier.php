<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToInvoicesFromSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_to_supplier_lines', function (Blueprint $table) {
            // $table->foreignId('invoices_from_supplier_id')->nullable()->constrained()->onUpdate('cascade')
            // ->onDelete('set null');
            $table->string('invoice_number')->nullable();
            $table->float('i_unit_quantity')->nullable();
            $table->float('i_unit_price')->nullable();
            $table->float('i_line_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_to_supplier_lines', function (Blueprint $table) {
            //
        });
    }
}
