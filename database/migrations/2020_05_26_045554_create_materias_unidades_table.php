<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMateriasUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materias_unidades', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('Materia_idMateria'); 
            $table->unsignedInteger('Unidad_idUnidad'); 
            $table->foreign('Materia_idMateria')->references('idMateria')->on('materias');
            $table->foreign('Unidad_idUnidad')->references('idUnidad')->on('unidades');
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
        Schema::dropIfExists('materias_unidades');
    }
}
