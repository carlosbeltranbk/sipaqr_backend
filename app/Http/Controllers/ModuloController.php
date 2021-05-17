<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


Use App\Modulo;
Use App\Rol;
Use App\User;

class ModuloController extends Controller
{
  
    public function obtenerModulo(Request $request)
    {
        $dia = 'Lunes';
        
        $email = $request->input('usuario');

        $nombreDia = $dia;
        // $fecha = date('Y-m-d');
        $fecha = '2020-05-04';

        $first = DB::select('call generarCodigoQR(?,?,?)',array($email,$fecha,$nombreDia));
        $second = DB::select('call obtenerFecha()');

        $collection = collect($first);
        $merged = $collection->merge($second);
        $result[] = $merged->all();

        return $result;
    }
}
