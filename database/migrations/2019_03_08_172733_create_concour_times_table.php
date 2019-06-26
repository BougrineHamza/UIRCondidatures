<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcourTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concour_times', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('concour_date_id');
            $table->integer('jury_id')->nullable();
            $table->integer('uir_formation_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('statut')->nullable();
            $table->string('time');
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
        Schema::dropIfExists('concour_times');
    }
}
