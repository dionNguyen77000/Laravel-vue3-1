<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesFromSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices_from_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orders_to_supplier_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('user');
            $table->string('invoice_img')->nullable();
            $table->string('img_two')->nullable();
            $table->string('img_three')->nullable();
            $table->string('invoice_img_thumbnail')->nullable();
            $table->string('supplier_invoice_number')->nullable();
            $table->datetime('received_date')->nullable();
            $table->float('total_price')->nullable();
            $table->string('supplier')->nullable();
            $table->string('Note')->nullable();
            $table->string('paid')->nullable();
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
        Schema::dropIfExists('invoices_from_suppliers');
    }
}
