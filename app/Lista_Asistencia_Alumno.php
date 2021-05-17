<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lista_Asistencia_Alumno extends Model
{
    public $timestamps = false;
   
   protected $fillable = ['idAsistencia', 'estado', 'Alumno_idAlumno', 'ListaAsistencia_idLista'];

}