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
            $table->foreignId('user_id')->constrained();;
            $table->foreignId('supplier_id')->constrained();;
            $table->foreignId('invoices_from_supplier_id')->constrained();
            $table->datetime('ordered_date');
            $table->integer('theory_price');
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
