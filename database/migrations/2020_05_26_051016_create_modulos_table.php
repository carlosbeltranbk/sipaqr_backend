<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->increments('idModulo');
            $table->string('nombreDia', 15);
            $table->string('horaInicio', 5);
            $table->string('horaFin', 5);
            $table->unsignedInteger('Materia_id'); 
            $table->unsignedInteger('Horario_idHorario'); 
            $table->foreign('Materia_id')->references('idMateria')->on('materias');
            $table->foreign('Horario_idHorario')->references('idHorario')->on('horarios');
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
        Schema::dropIfExists('modulos');
    }
}
