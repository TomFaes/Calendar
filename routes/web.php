<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
*/

Route::get('/home', 'HomeController@index');
Route::get('/', '\App\Http\Controllers\Season\SeasonGeneratorController@index')->name('home');

/** 
 * All Auth routes 
 * */
Auth::routes(['register' => false]);

Route::get( '/login/{social}', 'Auth\AuthenticationController@getSocialRedirect' )->middleware('guest');
Route::get( '/login/{social}/callback', 'Auth\AuthenticationController@getSocialCallback' )->middleware('guest');
Route::get( '/logout', 'Auth\AuthenticationController@logout' );

/** 
 * All User routes 
 * */
Route::resource('user', '\App\Http\Controllers\User\UserController')->middleware('admin')->except([
    'show'
]);

/** Profile routes */
Route::get('/profile/edit', '\App\Http\Controllers\User\ProfileController@edit')->name('profile.edit');
Route::put('/profile/update/', '\App\Http\Controllers\User\ProfileController@update')->name('profile.update');;

/** Password routes */
Route::put('/password/update-profile', '\App\Http\Controllers\PasswordController@updateProfilePassword');
Route::put('/password/update/{id}', '\App\Http\Controllers\PasswordController@updatePassword');

/** All Group routes */
Route::resource('group', '\App\Http\Controllers\User\GroupController')->except([
    'show'
]);

/** All GroupUser routes */
Route::resource('group.user', '\App\Http\Controllers\User\GroupUserController')->except([
    'create', 'show', 'edit', 'update'
]);

/**
 * Season Related routes
 */

/** All generate Season routes(must be before season) */
Route::resource('season/generate-season', '\App\Http\Controllers\Season\SeasonGeneratorController')->except([
    'create', 'store', 'show', 'destroy'
]);
/** All season routes */
Route::resource('season', '\App\Http\Controllers\Season\SeasonController');

/**All Team Routes*/
Route::resource('/season/{seasonId}/team', '\App\Http\Controllers\Season\TeamController')->except([
    'index', 'create', 'edit', 'show', 'update'
]);

/**All Absence Routes*/
Route::resource('/season/{seasonId}/absence', '\App\Http\Controllers\Season\AbsenceController')->except([
    'show', 'edit', 'create', 'update'
]);
