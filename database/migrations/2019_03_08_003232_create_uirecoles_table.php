<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUirecolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uir_ecoles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titre');
            $table->string('color');
            $table->string('logo_path');
            $table->string('emplacement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uir_ecoles');
    }
}
