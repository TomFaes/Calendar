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

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', '\App\Http\Controllers\Season\SeasonController@index');

/** All Auth routes */
Auth::routes(['register' => false]);

Route::get( '/login/{social}', 'Auth\AuthenticationController@getSocialRedirect' )->middleware('guest');
Route::get( '/login/{social}/callback', 'Auth\AuthenticationController@getSocialCallback' )->middleware('guest');
Route::get( '/logout', 'Auth\AuthenticationController@logout' );

/** All User routes */
Route::resource('user', '\App\Http\Controllers\User\UserController')->middleware('admin')->except([
    'show'
]);

/** Profile routes */
Route::get('/profile/edit', '\App\Http\Controllers\User\ProfileController@edit')->name('profile.edit');
Route::put('/profile/update/', '\App\Http\Controllers\User\ProfileController@update')->name('profile.update');;

/** Password routes */
Route::put('/password/update-profile', '\App\Http\Controllers\PasswordController@updateProfilePassword');
Route::put('/password/update/{id}', '\App\Http\Controllers\PasswordController@updatePassword');

/**All Team Routes*/
Route::resource('/season/{seasonId}/team', '\App\Http\Controllers\Season\TeamController')->except([
    'index', 'create', 'edit', 'show', 'update'
]);

/** All generate Season routes(must be before season) */
Route::resource('season/generate-season', '\App\Http\Controllers\Season\SeasonGeneratorController')->except([
    'create', 'store', 'show', 'destroy'
]);
/** All season routes */
Route::resource('season', '\App\Http\Controllers\Season\SeasonController');



//Route::get('season/generate-season/{season}', '\App\Http\Controllers\Season\SeasonGeneratorController@generateSeason')->name('generateSeason.create');
//Route::post('season/save-season/{season}', '\App\Http\Controllers\Season\SeasonGeneratorController@saveSeason')->name('generateSeason.store')->middleware('season');








/** All Group routes */
Route::resource('group', 'GroupsController')->except([
    'show'
]);

Route::get('/group/group-users/{group}', '\App\Http\Controllers\GroupsController@groupUsers')->middleware('group')->name('groupUsers');
Route::post('/group/add-users/{group}', '\App\Http\Controllers\GroupsController@addUsers')->middleware('group')->name('addUsers');
Route::post('/group/delete-user/{group}', '\App\Http\Controllers\GroupsController@deleteGroupUser')->name('deleteUser');

/** All season routes */
//Route::get('/season/next-game', '\App\Http\Controllers\SeasonsController@nextGame');
//Route::resource('season', 'SeasonsController');
//Route::get('/season/generateSeason/{season}', '\App\Http\Controllers\SeasonsController@generateSeason')->name('generateSeason');
//Route::post('/season/saveSeason/{season}', '\App\Http\Controllers\SeasonsController@saveSeason')->middleware('season');


/**All Absence Routes*/
Route::resource('absence', 'AbsencesController')->except([
    'index', 'edit', 'create', 'store'
]);