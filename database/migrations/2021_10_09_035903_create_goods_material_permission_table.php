<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsMaterialPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_material_permission', function (Blueprint $table) {
            $table->foreignId('goods_material_id')->constrained()->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onUpdate('cascade')
            ->onDelete('cascade');

            // $table->primary(['goods_material_id', 'permission_id'],'goods_material_permission_primary'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_material_permission');
    }
}
