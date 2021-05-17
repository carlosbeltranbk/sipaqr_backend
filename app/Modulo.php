<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    public $timestamps = false;
   
   protected $fillable = ['idModulo', 'nombreDia', 'horaInicio', 'horaFin'];

}
