<?php

use Illuminate\Support\Facades\Route;
use App\Http\Resources\DiasFeriadosCollection as ResourceCollection;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'response' => 'Funcionando...'
    ]);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
