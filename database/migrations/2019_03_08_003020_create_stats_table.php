<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->timestamp('last_login')->nullable();
            $table->string('last_ip')->nullable();
            // On devrait ajouter plus de stats (Nombre d'emails envoyés, ouverts, cliqués)
            // On devrait ajouter plus de stats (Nombre d'appels émis, nombre d'appels écoutés, nb appels échoués)
            $table->integer('lead_level')->default(0);
            $table->integer('total_time_spent')->nullable();
            $table->integer('total_connexions')->default(0);
            $table->string('signup_trafic_source')->nullable();
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
        Schema::dropIfExists('stats');
    }
}
