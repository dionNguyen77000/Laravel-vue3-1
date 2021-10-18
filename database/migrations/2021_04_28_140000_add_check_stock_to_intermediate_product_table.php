<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckStockToIntermediateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('intermediate_products', function (Blueprint $table) {
            $table->float('current_qty')->default(0);
            $table->float('prepared_point')->default(0);
            $table->float('coverage')->default(0);
            $table->float('required_qty')->default(0);
            $table->string('Preparation')->nullable();
            $table->tinyInteger('Active')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intermediate_products', function (Blueprint $table) {
            //
        });
    }
}
