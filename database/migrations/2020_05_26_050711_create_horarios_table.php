<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->increments('idHorario');
            $table->string('horaInicioReceso', 5);
            $table->string('horaFinReceso', 5);
            $table->unsignedInteger('Cuatrimestre_idCuatrimestre'); 
            $table->unsignedInteger('Grupo_idGrupo'); 
            $table->foreign('Cuatrimestre_idCuatrimestre')->references('idCuatrimestre')->on('cuatrimestres');
            $table->foreign('Grupo_idGrupo')->references('idGrupo')->on('grupos');
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
        Schema::dropIfExists('horarios');
    }
}
