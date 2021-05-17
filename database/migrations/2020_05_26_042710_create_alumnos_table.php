<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->increments('idAlumno');
            $table->string('nombre', 45);
            $table->string('apellido1', 45);
            $table->string('apellido2', 45);
            $table->string('correo', 45);
            $table->string('matricula', 45);
            $table->string('estado', 45);
            $table->unsignedInteger('Usuario_idUsuario'); 
            $table->unsignedInteger('Grupo_idGrupo'); 
            $table->foreign('Usuario_idUsuario')->references('idUsuario')->on('usuarios');
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
        Schema::dropIfExists('alumnos');
    }
}
