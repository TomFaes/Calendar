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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'SeasonsController@index');

/** All User routes */
Route::resource('user', '\App\Http\Controllers\User\UserController')->middleware('admin')->except([
    'show'
]);

/** Profile routes */
Route::get('/profile/update', '\App\Http\Controllers\User\ProfileController@edit');
Route::put('/profile/update/{id}', '\App\Http\Controllers\User\ProfileController@update');

/** Password routes */
Route::put('/password/update-profile', '\App\Http\Controllers\PasswordController@updateProfilePassword');
Route::put('/password/update/{id}', '\App\Http\Controllers\PasswordController@updatePassword');

/** All Group routes */
Route::resource('group', 'GroupsController')->except([
    'show'
]);

Route::get('/group/group-users/{group}', '\App\Http\Controllers\GroupsController@groupUsers')->middleware('group')->name('groupUsers');
Route::post('/group/add-users/{group}', '\App\Http\Controllers\GroupsController@addUsers')->middleware('group')->name('addUsers');
Route::post('/group/delete-user/{group}', '\App\Http\Controllers\GroupsController@deleteGroupUser')->name('deleteUser');

/** All season routes */
Route::get('/season/next-game', '\App\Http\Controllers\SeasonsController@nextGame');
Route::resource('season', 'SeasonsController');
Route::get('/season/generateSeason/{season}', '\App\Http\Controllers\SeasonsController@generateSeason')->name('generateSeason');
Route::post('/season/saveSeason/{season}', '\App\Http\Controllers\SeasonsController@saveSeason')->middleware('season');


/**All Absence Routes*/
Route::resource('absence', 'AbsencesController')->except([
    'index', 'edit', 'create', 'store'
]);

/** All teams */
Route::post('/season/calendar/addTeam/{id}', '\App\Http\Controllers\TeamsController@addTeam')->middleware('admin:Editor');
Route::get('/season/deleteTeam/{id}', '\App\Http\Controllers\TeamsController@destroy')->middleware('admin:Editor');