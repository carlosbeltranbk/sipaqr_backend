<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiaFeriado extends Model
{
   public $timestamps = false;
   
   protected $fillable = ['id', 'fecha', 'razon'];

}
