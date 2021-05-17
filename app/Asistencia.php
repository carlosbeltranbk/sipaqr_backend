<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    public $timestamps = false;
   
   protected $fillable = ['estado', 'horaDeAsistencia'];

}