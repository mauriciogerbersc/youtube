<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'YoutubeController@index')->name('pagina_inicial');
Route::post('/result', 'YoutubeController@buscar')->name('buscar_video');
Route::get('/watch/{id}/{seconds}', 'YoutubeController@assistir');
Route::get('/tempoUtilizado', 'YoutubeController@tempoUtilizado')->name('tempo_utilizado');
Route::get('/definirSessao', 'YoutubeController@definirSessao')->name('definir_sessao');