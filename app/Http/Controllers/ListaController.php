<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


Use App\Lista;
Use App\Docente;
Use App\Lista_Asistencia_Alumno;
Use App\Grupo;

class ListaController extends Controller
{

    public function obtenerGrupos(Request $request)
    {
        $idDocente = $request->input('idDocente');  
     
            return DB::select('call obtenerGrupo(?)',array($idDocente));
          
    }
  
    public function obtenerListas(Request $request)
    {
        $idDocente = $request->input('idDocente');  
        $fecha = $request->input('fecha');
        $nombre = $request->input('nombre');

        $value = Str::limit($fecha, 10);

        $checar = DB::table('listas_asistencias')->where('fecha',$value)->first();
     
        if(!$checar){
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No existen registros de listas con la fecha indicada.'])],404);
            
        }else{
            return DB::select('call obtenerListasFinal(?,?,?)',array($idDocente,$fecha,$nombre));
        }
            
    }

     public function busquedaID(Request $request)
    {
        $idAsistencia = $request->input('idAsistencia');
        try {
            return DB::select('call obtenerListasID(?)',array($idAsistencia));
            $queryStatus = "Si";
        } catch(Exception $e) {
            return $queryStatus = "No";
        }
    }

    public function modificarEstatus(Request $request)
    {
        $idAsistencia = $request->input('idAsistencia');
        $estado = $request->input('estado');
         DB::select('call modificarEstatus(?,?)',array($idAsistencia, $estado));

         $estatusModificado = DB::select('select * from listas_asistencias_alumnos order by idAsistencia desc');

        return response()->json(['status'=>'Se modificó correctamente.','data'=>$estatusModificado],200);
        
    }
}