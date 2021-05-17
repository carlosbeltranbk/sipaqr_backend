<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Exports\ReporteExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ReporteCollectionImport;
Use App\DiaFeriado;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function GuzzleHttp\json_decode;

class DiaFeriadoController extends Controller
{
    public function index()
    {
        $prueba = "algo";

        return DB::select('select * from dia_feriados order by razon');
        
    }

    public function show(DiaFeriado $diaFeriado)
    {
       // return $diaFeriado;
       $diaFeriado=diaFeriado::find($diaFeriado);

		// Si no existe ese fabricante devolvemos un error.
		if (! $diaFeriado)
		{
			// Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
			// En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
			return response()->json(['errors'=>array(['code'=>404,'message'=>'Registro no encontrado.'])],404);
		}else{
            return response()->json($diaFeriado,200);

        }
    }

    public function store(Request $request)
    {
        // Método llamado al hacer un POST.
        // Comprobamos que recibimos todos los campos.
       // $fecha = $request->input('fecha');
        //$fecha = strtotime($fecha);
        //$fecha = date('Y-m-d', strtotime($request->input('fecha')));
        //$date->toDateString();  
        //Carbon::createFromFormat('d/m/Y', $request->input('fecha'));
        
		if (!$request->input('fecha') || !$request->input('razon'))
		{
			// NO estamos recibiendo los campos necesarios. Devolvemos error.
            return response()->json(['errors'=>array(['code'=>422,'message'
            =>'Faltan datos necesarios para procesar el registros correctamente.'])],422);
		}else {
            // Insertamos los datos recibidos en la tabla.
            
  
            $fecha = $request->input('fecha');
            $razon = $request->input('razon');
            DB::select('call crearDiaFeriado(?,?)',array($fecha,$razon));
            $diaFeriado = DB::select('select * from dia_feriados order by id desc');

        return response()->json(['status'=>'Se registro correctamente.','data'=>$diaFeriado],201);
		// Devolvemos la respuesta Http 201 (Created) + los datos del nuevo fabricante + una cabecera de Location + cabecera JSON
        }
    }

    

    public function update(Request $request, DiaFeriado $diaFeriado)
    {
        
        if (!$request->input('fecha') || !$request->input('razon'))
		{
			// NO estamos recibiendo los campos necesarios. Devolvemos error.
            return response()->json(['errors'=>array(['code'=>422,'message'
            =>'Ocurrio un error, intante de nuevo.'])],422);
		} else{

            $id = $request->input('id');
            $fecha = $request->input('fecha');
            $razon = $request->input('razon');

            DB::select('call modificarDiaFeriado(?,?,?)',array($id,$fecha,$razon));

            $diaFeriado = DB::select('select * from dia_feriados order by id desc');

            return response()->json(['status'=>'Se modificó correctamente.','data'=>$diaFeriado],200);
        }
    }

    public function delete(DiaFeriado $diaFeriado){

        $diaFeriado->delete();
        return response()->json(['status'=>'Se eliminó correctamente.','data'=>$diaFeriado],204);
        
    }

    public function export(Request $request) {

        $idMateria = $request->input('idMateria');
        $nombreGrupo = $request->input('nombreGrupo');
        
        //$idMateria = 1; // id docentes_materias
        //$nombreGrupo = "9°A"; // nombre del grupo
        
        $asistenciasTotales = 0; // aux % asistencias

        $jsonDiasFeriados = json_decode(json_encode(DB::select('select * from dia_feriados order by id desc')));

        $jsonCuatrimestre = json_decode(json_encode(
            DB::select(' SELECT c.fechaInicio, c.fechaFin, dm.id FROM cuatrimestres c 
            JOIN horarios h ON h.Cuatrimestre_idCuatrimestre = c.idCuatrimestre
                JOIN modulos modu ON modu.Horario_idHorario = h.idHorario
                    JOIN docentes_materias dm ON dm.id = modu.Materia_id
                        WHERE dm.id = '. $idMateria .' GROUP BY c.fechaInicio, c.fechaFin, dm.id;')));

        $unidades = json_encode(DB::select('SELECT u.nombre, u.numeroUnidad,u.horas, u.fechaInicio, u.fechaFin, dm.id FROM unidades u 
        JOIN materias_unidades um ON um.Unidad_idUnidad = u.idUnidad
            JOIN materias m ON m.idMateria = um.Materia_idMateria
                JOIN docentes_materias dm ON dm.Materia_idMateria = m.idMateria
                    WHERE dm.id ='.$idMateria . ';'));

        $jsonUnidades = json_decode($unidades);
        
        $diasModulo = json_encode(
        DB::select('SELECT modu.nombreDia FROM modulos modu 
        JOIN docentes_materias dm ON dm.id = modu.Materia_id
            WHERE dm.id = '. $idMateria .';')
        );

        $jsonDiasModulos = json_decode($diasModulo);


        $diasClases = collect();
        $contador = 0;
        setlocale(LC_TIME, "spanish");
        for($i=$jsonCuatrimestre[0]->fechaInicio;$i<=$jsonCuatrimestre[0]->fechaFin;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){
            for($c = 0;$c < count($jsonDiasModulos); $c++){
                if( utf8_decode(strtolower($jsonDiasModulos[$c]->nombreDia)) === utf8_decode(strftime("%A", strtotime($i)))){
                    $diaFeriado = false;
                    for($d = 0; $d < count($jsonDiasFeriados); $d++){
                        if($i === $jsonDiasFeriados[$d]->fecha){
                            $diaFeriado = true;
                        }
                    }
                    if($diaFeriado === false){

                        $diasClases->put($contador,$i);
                        $contador = $contador + 1;
                    }
                }
            }
        }


        $alumnos = json_encode(
        DB::select('SELECT a.idAlumno, a.apellido1, a.apellido2,a.nombre, g.nombre as grupo
        FROM Alumnos a JOIN grupos g ON
            a.Grupo_idGrupo = g.idGrupo
                WHERE g.nombre ="'. $nombreGrupo .'"ORDER BY a.apellido1, a.apellido2, a.nombre ASC;')
        );

        $jsonAlumnos = json_decode($alumnos);
        $jsonAlumnosAsistencias = json_decode('[]');
        $jsonAlumnosAsistencias[0] = json_decode('{"nombre":"","asistencias":[]}');

        

        for($c=0; $c<count($jsonAlumnos); $c++){
            $jsonAlumnosAsistencias[$c] = json_decode('{"nombre":"","asistencias":[]}');
            $jsonAlumnosAsistencias[$c]->nombre = $jsonAlumnos[$c]->apellido1 . " " . $jsonAlumnos[$c]->apellido2 . " " . $jsonAlumnos[$c]->nombre;
            $asistenciasGeneralesAlumno = 
            json_encode(DB::select('SELECT a.nombre,a.apellido1,a.apellido2,laa.estado,la.fecha FROM materias m JOIN docentes_materias dm ON dm.Materia_idMateria = m.idMateria
            JOIN docentes d ON d.idDocente = dm.Docente_idDocente
                JOIN modulos modu ON dm.id = modu.Materia_id
                    JOIN listas_asistencias la ON la.Modulo_idModulo = modu.idModulo
                        JOIN listas_asistencias_alumnos laa ON laa.ListaAsistencia_idLista = la.idLista
                            JOIN alumnos a ON a.idAlumno = laa.Alumno_idAlumno
                                JOIN grupos g ON a.Grupo_idGrupo = g.idGrupo 
                                    WHERE a.idAlumno = ' .$jsonAlumnos[$c]->idAlumno .' AND dm.id = '. $idMateria . ';'))
            ;

            $jsonAsistenciasGeneralesAlumno = json_decode($asistenciasGeneralesAlumno);
            $asistencias[] = array();
            $numeroSemana = 0;
            $semanaActual =date("W",strtotime( $jsonCuatrimestre[0]->fechaInicio)); 
            $clasesImpartidas = 0;
            for($b = 0; $b < count($diasClases); $b++ ){
                $semanaFecha = date("W",strtotime(  $diasClases->get($b) ));
                if($semanaActual < $semanaFecha){
                    $numeroSemana++;
                    $semanaActual =  $semanaFecha;
                }
                $asistencias[$b]["semana"] = $numeroSemana;
                $asistencias[$b]["fecha"] =  $diasClases->get($b);
                for($a = 0; $a < count($jsonAsistenciasGeneralesAlumno ); $a++){
                    if( $jsonAsistenciasGeneralesAlumno[$a]->fecha === $diasClases->get($b) ){
                        $clasesImpartidas++;
                        switch($jsonAsistenciasGeneralesAlumno[$a]->estado){

                            case "Asistencia":
                                $asistencias[$b]["estado"] = ".";
                            break;
                            case "Inasistencia":
                                $asistencias[$b]["estado"] = "/";
                            break;
                            case "Justificado":
                                $asistencias[$b]["estado"] = "X";
                            break;
                        }
                    break;
                    }else{
                        $asistencias[$b]["estado"] = " " ;
                    }
                    
                }
            }
            $jsonAlumnosAsistencias[$c]->asistencias = $asistencias;
        }


        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet =  $reader->load(storage_path('portafolio.xlsx')) ;
        $sheet = $spreadsheet->getActiveSheet();
        $numCelda = 13;

        $semanInicialCuatri = date("W",strtotime( $jsonCuatrimestre[0]->fechaInicio));
        $clasesTotales = count($diasClases);

        for($i = 0; $i<count($jsonAlumnosAsistencias); $i++){
            $celda = "B" . $numCelda;

            $sheet->getCell($celda)
            ->setValue($jsonAlumnosAsistencias[$i]->nombre);

            $periodo1 = 67;
            $periodo2 = 89;
            $periodo3 = 85;
            $celdaHor2 = 65;
            $celdaExtra = "";
            $celdaAux = 65;
            $contadorInicioUnidad = 0;
            $contadorFinUnidad = 0;
            $asistenciasPorUnidad = 0;
            $mesInicial = "";
            $mesFinal = "";
            for($c = 0; $c<count($diasClases); $c++){
                $celdaAistencia =  $celdaExtra ;
                $celdaAistencia2 =  $celdaExtra ;
                if($contadorInicioUnidad< count( $jsonUnidades) ){
                    if( $jsonUnidades[$contadorInicioUnidad]->fechaInicio == $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]){
                        $asistenciasTotales = 0; 
                    }
                }
                /*if($jsonAlumnosAsistencias[$i]->asistencias[$c]["estado"] != " "){*/
                    
                    $semanaFecha = date("W",strtotime( $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]));
                    if($jsonAlumnosAsistencias[$i]->asistencias[$c]["estado"] == "." || $jsonAlumnosAsistencias[$i]->asistencias[$c]["estado"] == "X"){
                        $asistenciasPorUnidad++;
                    }
                        

                    if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] <= 5 ){
                        if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] == 1){
                            $mesInicial =  ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][(date('m', strtotime($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"])))-1];
                        }else if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] == 5){
                            $mesFinal =  ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][(date('m', strtotime($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"])))-1];
                            $sheet->getCell("C9")
                            ->setValue($mesInicial . "-" .   $mesFinal);
                        }


                        $celdaAistencia = $celdaAistencia . "" .chr($periodo1) ."". $numCelda;
                        $celdaAistencia2 = $celdaAistencia2 . "" .chr($periodo1) ."11";
                        $diaFecha = date("d",strtotime( $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]));
                        $sheet->getCell(  $celdaAistencia2 )
                        ->setValue($diaFecha);


                        if($contadorFinUnidad< count( $jsonUnidades) ){
                            if(($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"] >= $jsonUnidades[$contadorFinUnidad]->fechaInicio)&&($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"] <= $jsonUnidades[$contadorFinUnidad]->fechaFin)){
                                $asistenciasTotales++;
                            }
                            if( $jsonUnidades[$contadorFinUnidad]->fechaFin == $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]){
                                $periodo1++;
                                $porcentaje = (100/$asistenciasTotales) *$asistenciasPorUnidad;
                                $sheet->getCell( $celdaExtra  . "" .chr($periodo1) ."". $numCelda)
                                ->setValue(round($porcentaje));
                                $sheet->getStyle( $celdaExtra  . "" .chr($periodo1) ."". $numCelda)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E5EA');
                                $sheet->getColumnDimension($celdaExtra  . "" .chr($periodo1) )
                                ->setAutoSize(true);
                                $sheet->getCell( $celdaExtra  . "" .chr($periodo1)  ."11")
                                ->setValue("%");
                                $sheet->getStyle( $celdaExtra  . "" .chr($periodo1)  ."11")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E5EA');

                                $asistenciasTotales = 0;
                                $asistenciasPorUnidad = 0;  
                                $contadorFinUnidad++;
                            }
                        }
                        if($contadorInicioUnidad< count( $jsonUnidades) ){
                            if( $jsonUnidades[$contadorInicioUnidad]->fechaInicio == $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]){

                                $sheet->getCell(  $celdaExtra . "" .chr($periodo1) ."10")
                                ->setValue($jsonUnidades[$contadorInicioUnidad]->numeroUnidad);
                                $contadorInicioUnidad++;
                            }
                        }
                        $periodo1++;
                        if($periodo1 > 90){
                            $celdaExtra = chr($celdaHor2);
                            $celdaHor2++;
                            $periodo1 = 65;
                        }
                    }else if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] <= 10 ){
                        if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] == 6){
                            $mesInicial =  ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][(date('m', strtotime($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"])))-1];
                        }else if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] == 10){
                            $mesFinal =  ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][(date('m', strtotime($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"])))-1];
                            $sheet->getCell("Y9")
                            ->setValue($mesInicial . "-" .   $mesFinal);
                        }

                        $celdaAistencia = $celdaAistencia . "" .chr($periodo2) ."". $numCelda;
                        $celdaAistencia2 = $celdaAistencia2 . "" .chr($periodo2) ."11";
                        $diaFecha = date("d",strtotime( $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]));
                        $sheet->getCell($celdaAistencia2)
                        ->setValue($diaFecha);
                        if($contadorFinUnidad< count( $jsonUnidades) ){
                            if(($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"] >= $jsonUnidades[$contadorFinUnidad]->fechaInicio)&&($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"] <= $jsonUnidades[$contadorFinUnidad]->fechaFin)){
                                $asistenciasTotales++;
                            }
                            if( $jsonUnidades[$contadorFinUnidad]->fechaFin == $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]){
                                
                                $periodo2++;
                                $porcentaje = (100/$asistenciasTotales)*$asistenciasPorUnidad;
                                $sheet->getCell( $celdaExtra  . "" .chr($periodo2) ."". $numCelda)
                                ->setValue(round($porcentaje) );
                                $sheet->getStyle( $celdaExtra  . "" .chr($periodo2) ."". $numCelda)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E5EA');
                                $sheet->getColumnDimension($celdaExtra  . "" .chr($periodo2) )
                                ->setAutoSize(true);
                                $sheet->getCell( $celdaExtra  . "" .chr($periodo2)  ."11")
                                ->setValue("%");
                                $sheet->getStyle( $celdaExtra  . "" .chr($periodo2)  ."11")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E5EA');

                                $contadorFinUnidad++;
                                $asistenciasTotales = 0;
                                $asistenciasPorUnidad = 0; 
                            }
                        }
                        if($contadorInicioUnidad< count( $jsonUnidades) ){

                            if( $jsonUnidades[$contadorInicioUnidad]->fechaInicio == $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]){

                                $sheet->getCell(  $celdaExtra . "" .chr($periodo2) ."10")
                                ->setValue($jsonUnidades[$contadorInicioUnidad]->numeroUnidad);
                                $contadorInicioUnidad++;
                            }
                        }
                        
                        $periodo2++;
                        if($periodo2 > 90){
                            $celdaExtra = chr($celdaHor2);
                            $celdaHor2++;
                            $periodo2 = 65;
                        }
                        
                    }else if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] <= 15 ){
                        if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] == 11){
                            $mesInicial =  ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][(date('m', strtotime($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"])))-1];
                        }else if($jsonAlumnosAsistencias[$i]->asistencias[$c]["semana"] == 15){
                            $mesFinal =  ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][(date('m', strtotime($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"])))-1];
                            $sheet->getCell("AU9")
                            ->setValue($mesInicial . "-" .   $mesFinal);
                        }

                        $celdaAistencia = chr($celdaAux) . "" .chr($periodo3) ."". $numCelda;
                        $diaFecha = date("d",strtotime( $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]));
                        $sheet->getCell( chr($celdaAux) . "" .chr($periodo3) ."11" )
                        ->setValue($diaFecha);

                        if($contadorFinUnidad< count( $jsonUnidades) ){
                            if(($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"] >= $jsonUnidades[$contadorFinUnidad]->fechaInicio)&&($jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"] <= $jsonUnidades[$contadorFinUnidad]->fechaFin)){
                                $asistenciasTotales++;
                            }
                            if( $jsonUnidades[$contadorFinUnidad]->fechaFin == $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]){
                                
                                $periodo3++;
                                $porcentaje = (100/$asistenciasTotales)*$asistenciasPorUnidad;
                                $sheet->getCell( chr($celdaAux)   . "" .chr($periodo3) ."". $numCelda)
                                ->setValue(round($porcentaje));
                                $sheet->getStyle( chr($celdaAux)   . "" .chr($periodo3) ."". $numCelda)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E5EA');

                                $sheet->getStyle( chr($celdaAux)   . "" .chr($periodo3) ."". $numCelda);
                                $sheet->getColumnDimension(chr($celdaAux)   . "" .chr($periodo3) )
                                ->setAutoSize(true);
                                $sheet->getCell( chr($celdaAux)   . "" .chr($periodo3) ."11")
                                ->setValue("%");
                                $sheet->getStyle( chr($celdaAux)   . "" .chr($periodo3) ."11")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E5EA');

                                $sheet->getColumnDimension(chr($celdaAux)   . "" .chr($periodo3) )
                                ->setAutoSize(true);
                                $contadorFinUnidad++;
                                $asistenciasTotales = 0;
                                $asistenciasPorUnidad = 0; 
                            }
                        }
                        if( $contadorInicioUnidad< count( $jsonUnidades) ){
                            if( $jsonUnidades[$contadorInicioUnidad]->fechaInicio == $jsonAlumnosAsistencias[$i]->asistencias[$c]["fecha"]){

                                $sheet->getCell( chr($celdaAux) . "" .chr($periodo3) ."10")
                                ->setValue($jsonUnidades[$contadorInicioUnidad]->numeroUnidad);
                                $contadorInicioUnidad++;
                            }
                        }
                        $periodo3++;
                        if($periodo3 > 90){
                            $celdaAux++;
                            $periodo3 = 65;
                        }
                        
                    }else{
                    break;
                    }
                    $sheet->getCell($celdaAistencia)
                    ->setValue($jsonAlumnosAsistencias[$i]->asistencias[$c]["estado"]);
                    
                /*}*/

                
            }
            $numCelda++;
        }
        
        $jsonInfo = json_decode(json_encode(
        DB::select('
        SELECT d.nombre, d.apellido1,d.apellido2, g.nombre as grupo ,m.nombre as materia, c.nombre as cuatrimestre, g.nombreCarrera as carrera FROM docentes d 
                    JOIN docentes_materias dm ON  d.idDocente = dm.Docente_idDocente
                        JOIN materias m ON dm.Materia_idMateria = m.idMateria
                            JOIN modulos modu ON dm.id = modu.Materia_id
                                JOIN horarios h ON modu.Horario_idHorario = h.idHorario
                                    JOIN grupos g ON h.Grupo_idGrupo = g.idGrupo
                                        JOIN cuatrimestres c ON h.Cuatrimestre_idCuatrimestre = c.idCuatrimestre
                                            WHERE dm.id = ' . $idMateria  .'
                                                group by d.nombre, d.apellido1,d.apellido2, grupo , materia, cuatrimestre, carrera  ;')
        ));

        $sheet->getCell("M4")
                        ->setValue($jsonInfo[0]->materia);
        $sheet->getCell("M5")
                        ->setValue($jsonInfo[0]->nombre . " " . $jsonInfo[0]->apellido1 . " " . $jsonInfo[0]->apellido2  );
        $sheet->getCell("AT5")
                        ->setValue($jsonInfo[0]->cuatrimestre);
        $sheet->getCell("AT6")
                        ->setValue(substr($nombreGrupo,0,1) . substr($nombreGrupo,1,2));  
        $sheet->getCell("AT7")
                        ->setValue(substr($jsonInfo[0]->grupo, 3));
        $sheet->getCell("M3")
                        ->setValue($jsonInfo[0]->carrera);

        $sheet->getCell("BI48")
        ->setValue($clasesImpartidas ."/".$clasesTotales);



        $writer = new Xlsx($spreadsheet);

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Portafolio_'.$jsonInfo[0]->nombre . "_" . $jsonInfo[0]->apellido1 . "_" . $jsonInfo[0]->apellido2. "_".  str_replace("-","_",str_replace(" ","_",$jsonInfo[0]->cuatrimestre)) . '.xlsx"');
        $writer->save('php://output');
    }
}