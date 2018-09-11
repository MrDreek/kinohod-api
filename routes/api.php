<?php

use Illuminate\Http\Request;

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

Route::get('get-city-list','Api@getCityList')->name('city-list');
Route::post('get-code','Api@getCode')->name('get-code');
Route::get('get-movie-list/{code}','Api@getMovieListByCode')->name('get-movie-list-by-code');
Route::get('get-movie-list','Api@getMovieList')->name('get-movie-list');
Route::get('get-movie-detail/{id}','Api@getMovieDetail')->name('get-movie-detail');
Route::get('get-seances/{code}/{movieId}','Api@getseances')->name('get-seances');
