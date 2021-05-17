<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocentesMateriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docentes_materias', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Docente_idDocente'); 
            $table->unsignedInteger('Materia_idMateria'); 
            $table->foreign('Docente_idDocente')->references('idDocente')->on('docentes');
            $table->foreign('Materia_idMateria')->references('idMateria')->on('materias');
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
        Schema::dropIfExists('docentes_materias');
    }
}
