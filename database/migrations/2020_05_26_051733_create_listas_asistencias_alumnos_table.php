<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListasAsistenciasAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listas_asistencias_alumnos', function (Blueprint $table) {
            $table->increments('idAsistencia');
            $table->string('estado', 20);
            $table->unsignedInteger('Alumno_idAlumno'); 
            $table->unsignedInteger('ListaAsistencia_idLista'); 
            $table->string('horaDeAsistencia', 5);
            $table->foreign('Alumno_idAlumno')->references('idAlumno')->on('alumnos');
            $table->foreign('ListaAsistencia_idLista')->references('idLista')->on('listas_asistencias');
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
        Schema::dropIfExists('listas_asistencias_alumnos');
    }
}
