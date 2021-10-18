<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_materials', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->integer('price')->nullable();
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreignId('supplier_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained()->onUpdate('cascade')
            ->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
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
        Schema::dropIfExists('goods_materials');
    }
}
