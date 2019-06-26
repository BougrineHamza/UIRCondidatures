<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClonedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloneds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('num_etudiant')->unique();
            $table->string('email')->nullable();
            $table->integer('gender')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('cin')->nullable();
            $table->string('city_id')->nullable();
            $table->string('country_id')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('specialite_id')->nullable();
            $table->string('niveau')->nullable();
            $table->integer('annee_diplome')->nullable();

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
        Schema::dropIfExists('cloneds');
    }
}
