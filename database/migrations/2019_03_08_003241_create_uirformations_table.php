<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUirformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uir_formations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uirecole_id');
            $table->string('titre');
            $table->text('prerequis_html');
            $table->text('sur_dossier')->nullable();
            $table->integer('condition_niveau')->default(0);
            $table->string('condition_specialites')->nullable();
            $table->string('code_agresso')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uir_formations');
    }
}
