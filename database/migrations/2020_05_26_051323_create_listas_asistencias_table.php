<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListasAsistenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listas_asistencias', function (Blueprint $table) {
            $table->increments('idLista');
            $table->date('fecha');
            $table->unsignedInteger('Modulo_idModulo'); 
            $table->foreign('Modulo_idModulo')->references('idModulo')->on('modulos');
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
        Schema::dropIfExists('listas_asistencias');
    }
}
