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
Route::get('/', 'HomeController@index');
//Route::get('/', '\App\Http\Controllers\Season\SeasonGeneratorController@index')->name('home');

//Auth::routes(['register' => false]);

Route::get( '/login/{social}', 'Auth\AuthenticationController@getSocialRedirect' )->middleware('guest');
Route::get( '/login/{social}/callback', 'Auth\AuthenticationController@getSocialCallback' )->middleware('guest');
Route::get( '/logout', 'Auth\AuthenticationController@logout' );
Route::post('login/standard', 'Auth\AuthenticationController@login');


//vue router will handle all routing

Route::get('/{any}', function () {
    return view('home');
})->where('any', '.*');




//old login route, still default with some people
//Route::get('/season/next-game', '\App\Http\Controllers\Season\SeasonGeneratorController@index');

/** 
 * All Auth routes 
 * */
/*
Auth::routes(['register' => false]);

Route::get( '/login/{social}', 'Auth\AuthenticationController@getSocialRedirect' )->middleware('guest');
Route::get( '/login/{social}/callback', 'Auth\AuthenticationController@getSocialCallback' )->middleware('guest');
Route::get( '/logout', 'Auth\AuthenticationController@logout' );
*/
/** 
 * All User routes 
 * */
/*
Route::resource('user', '\App\Http\Controllers\User\UserController', ['parameters' => [
    'user' => 'id'
]])->middleware('admin')->except([
    'show'
]);

/** Profile routes */
/*
Route::get('/profile/edit', '\App\Http\Controllers\User\ProfileController@edit')->name('profile.edit');
Route::put('/profile/update/', '\App\Http\Controllers\User\ProfileController@update')->name('profile.update');;


/** Password routes */
/*
Route::put('/password/update-profile', '\App\Http\Controllers\PasswordController@updateProfilePassword');
Route::put('/password/update/{id}', '\App\Http\Controllers\PasswordController@updatePassword');

/** All Group routes */
/*
Route::resource('group', '\App\Http\Controllers\User\GroupController', ['parameters' => [
    'group' => 'id'
]])->except([
    'show'
]);
*/
/** All GroupUser routes */
/*
Route::resource('group.user', '\App\Http\Controllers\User\GroupUserController', ['parameters' => [
    'id' => 'id'
]])->except([
    'create', 'show', 'edit', 'update'
]);
*/
/**
 * Season Related routes
 */

/** All generate Season routes(must be before season) */
/*
Route::resource('season/{season}/generate-season', '\App\Http\Controllers\Season\SeasonGeneratorController')->except([
    'show', 'edit', 'update', 'destroy'
]);
*/
/** All season routes */
/*
Route::resource('season', '\App\Http\Controllers\Season\SeasonController', ['parameters' => [
    'season' => 'id'
]]);
*/
/**All Team Routes*/
/*
Route::resource('/season/{season}/team', '\App\Http\Controllers\Season\TeamController', ['parameters' => [
    'team' => 'id'
]])->except([
    'index', 'create', 'edit', 'show', 'update'
]);
*/
/**All Absence Routes*/
/*
Route::resource('/season/{season}/absence', '\App\Http\Controllers\Season\AbsenceController', ['parameters' => [
    'absence' => 'id'
]])->except([
    'show', 'edit', 'create', 'update'
]);
*/
