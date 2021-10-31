<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiscellaneousInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('miscellaneous_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->string('img')->nullable();
            $table->string('img_two')->nullable();
            $table->string('img_three')->nullable();
            $table->string('img_thumbnail')->nullable();
            $table->string('supplier_invoice_number')->nullable();
            $table->datetime('received_date')->nullable();
            $table->float('total_price')->nullable();
            $table->string('supplier')->nullable();
            $table->string('Note')->nullable();
            $table->string('paid')->nullable();
            $table->string('invoice_category')->nullable();
            $table->string('invoice_type')->nullable();
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
        Schema::dropIfExists('miscellaneous_invoices');
    }
}
