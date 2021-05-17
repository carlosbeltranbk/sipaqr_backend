<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->increments('idDocente');
            $table->string('nombre', 45);
            $table->string('apellido1', 45);
            $table->string('apellido2', 45);
            $table->string('correo', 45);
            $table->string('matricula', 45);
            $table->string('rfc', 20);
            $table->unsignedInteger('Usuario_idUsuario'); 
            $table->foreign('Usuario_idUsuario')->references('idUsuario')->on('usuarios');
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
        Schema::dropIfExists('docentes');
    }
}
