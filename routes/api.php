<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Use App\DiaFeriado;

// Route::get('diasFeriados', 'DiaFeriadoController@index');
// Route::get('diasFeriados/{id}', 'DiaFeriadoController@show');
// Route::post('diasFeriados', 'DiaFeriadoController@store');
// Route::put('diasFeriados/{id}', 'DiaFeriadoController@update');
// Route::delete('diasFeriados/{id}', 'DiaFeriadoController@delete');
// ------------------------------ RUTAS DE DIAS FERIADOS------------------------------------------ //

Route::get('diasFeriados', 'DiaFeriadoController@index');
Route::get('diasFeriados/{diaFeriado}', 'DiaFeriadoController@show');
Route::post('diasFeriados', 'DiaFeriadoController@store');
Route::post('modificarDia', 'DiaFeriadoController@update');
Route::delete('diasFeriados/{diaFeriado}', 'DiaFeriadoController@delete');

// ------------------------------ RUTAS DE DIAS MODULOS------------------------------------------ //

Route::post('modulos', 'ModuloController@obtenerModulo');

// ------------------------------ RUTAS PARA PASAR LISTA------------------------------------------ //

Route::post('pasarLista', 'AsistenciaController@pasarLista');

// ------------------------------ RUTAS PARA GENERAR LISTAS DE ASISTENCIAS------------------------------------------ //

Route::post('listasAlumnos', 'ListaController@obtenerListas');
Route::post('gruposDocentes', 'ListaController@obtenerGrupos');
//Route::post('listasAlumnos', 'ListaController@busquedaID');
Route::post('modificacionAlumnoEstatus', 'ListaController@modificarEstatus');

// ------------------------------ RUTAS DE INICIO DE SESIÓN------------------------------------------ //
 	
Route::resource('user', 'UserController',
                ['only' => ['index', 'store', 'update', 'destroy', 'show']]);


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
  
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});


Route::post('reporte', 'DiaFeriadoController@export'); // -------