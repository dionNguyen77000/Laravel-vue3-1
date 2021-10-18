<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntermediateProductPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intermediate_product_permission', function (Blueprint $table) {
            $table->foreignId('intermediate_product_id')->constrained()->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intermediate_product_permission');
    }
}
