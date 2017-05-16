<?php

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
    $videos = App\Video::all();
    return $videos;
});

Route::get('/search', function () {
    return view('general.search');
});

Route::get('/videoss', 'VideosController@format');

Route::get('/videos', 'VideosController@index');
Route::get('/videos/{video}', 'VideosController@show');
