<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersToSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_to_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->string('supplier');
            $table->string('invoice_number');
            $table->datetime('ordered_date')->nullable();
            $table->float('estimated_price')->nullable();
            $table->string('excel_file')->nullable();
            $table->string('Note')->nullable();
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
        Schema::dropIfExists('orders_to_suppliers');
    }
}
