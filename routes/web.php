<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Language Switcher
Route::get('locale/{locale}', 'LocaleController@switch')->name('locale.switch');

// Authentication Routes
Route::get('login', 'AuthController@showLoginForm')->name('login');
Route::post('login', 'AuthController@login')->name('login.post');
Route::post('logout', 'AuthController@logout')->name('logout');

// Authenticated Routes
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return redirect()->route('movies.index');
    });

    // Movies
    Route::get('movies', 'MovieController@index')->name('movies.index');
    Route::get('movies/search', 'MovieController@search')->name('movies.search');
    Route::get('movies/{imdbId}', 'MovieController@show')->name('movies.show');

    // Favorites
    Route::get('favorites', 'FavoriteController@index')->name('favorites.index');
    Route::post('favorites', 'FavoriteController@store')->name('favorites.store');
    Route::delete('favorites/{imdbId}', 'FavoriteController@destroy')->name('favorites.destroy');
});
