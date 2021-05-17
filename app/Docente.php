<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    public $timestamps = false;
   
   protected $fillable = ['idDocente', 'nombre', 'apellido1', 'apellido2', 'correo', 'matricula', 'rfc'];

}