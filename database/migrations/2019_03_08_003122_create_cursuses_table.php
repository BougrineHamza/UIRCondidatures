<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCursusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('specialite_id')->nullable();
            $table->string('niveau')->nullable();
            $table->integer('annee_diplome')->nullable();
            $table->string('countryedu_id');
            $table->string('cityedu_id');
            $table->string('systemedu_id');
            $table->string('typedu');
            $table->string('school');
            $table->string('mes_formations_uir')->nullable();
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
        Schema::dropIfExists('cursuses');
    }
}
