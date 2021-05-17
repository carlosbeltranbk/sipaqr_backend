<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    public $timestamps = false;
   
   protected $fillable = ['idAlumno', 'nombre', 'apellido1', 'apellido2', 
   'correo', ' matricula ', 'estado'];

}