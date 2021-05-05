<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyEmpWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_emp_works', function (Blueprint $table) {
            $table->datetime('date');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('intermediate_product_id')->nullable()->constrained();
            $table->primary(['user_id', 'intermediate_product_id','date'],'user_intermediate_date_primary');
            $table->float('doneByEmployee')->nullable();
            $table->float('current_prepared_qty')->nullable();
            $table->float('required_qty')->nullable();
            $table->string('Status')->nullable();
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
        Schema::dropIfExists('daily_emp_works');
    }
}
