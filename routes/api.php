<?php

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


Route::post('get-code','Api@getCode')->name('get-code');
Route::post('get-movie-list','Api@getMovieListByCode')->name('get-movie-list-by-code');
Route::post('get-movie-detail','Api@getMovieDetail')->name('get-movie-detail');
Route::post('get-seances','Api@getseances')->name('get-seances');

Route::get('get-city-list','Api@getCityList')->name('city-list');
Route::get('get-movie-list','Api@getMovieList')->name('get-movie-list');
Route::get('get-cinema-list','Api@getCinemaList')->name('get-cinema-list');
