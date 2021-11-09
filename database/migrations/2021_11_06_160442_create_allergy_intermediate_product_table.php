<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllergyIntermediateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allergy_intermediate_product', function (Blueprint $table) {
            $table->foreignId('allergy_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
            $table->foreignId('intermediate_product_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allergy_intermediate_product');
    }
}
