<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



Use App\Modulo;
Use App\Alumno;
Use App\Lista;
Use App\Asistencia;

class AsistenciaController extends Controller
{
  
    public function pasarLista(Request $request)
    {

            $idModulo = $request->input('idModulo');
            $grupoNombre = $request->input('grupoNombre');
            $idAlumno = $request->input('idAlumno');
            $idLista = $request->input('idLista');


            DB::select('call tomarAsistencia(?,?,?,?)',array($idModulo,$grupoNombre,$idAlumno,$idLista));
         
            $asistencia = DB::select('select * from listas_asistencias_alumnos order by idAsistencia desc');

            return response()->json(['status'=>'Exito','data'=>$asistencia],201);

        
    }
}