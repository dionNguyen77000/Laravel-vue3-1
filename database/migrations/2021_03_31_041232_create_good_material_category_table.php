<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodMaterialCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_material_category', function (Blueprint $table) {
        
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->foreignId('goods_materials_id')->constrained()->onDelete('cascade');

            $table->primary(['category_id', 'goods_materials_id']);

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
        Schema::dropIfExists('good_material_category');
    }
}
