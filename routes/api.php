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




Route::get('profile', '\App\Http\Controllers\User\ProfileController@index');
Route::post('profile', '\App\Http\Controllers\User\ProfileController@update');
Route::delete('profile', '\App\Http\Controllers\User\ProfileController@destroy');

/**
 * All routes for a user
 */
/*
Route::post('user/{id}', '\App\Http\Controllers\User\UserController@update');
Route::resource('user', '\App\Http\Controllers\User\UserController')->except([
    'create', 'edit'
]);
*/
Route::post('user/{id}', '\App\Http\Controllers\User\UserController@update');
Route::resource('user', '\App\Http\Controllers\User\UserController',  ['parameters' => [
    'user' => 'id'
]])->except([
    'create', 'edit'
]);

Route::post('group/{id}', '\App\Http\Controllers\Group\GroupController@update');
Route::resource('group', '\App\Http\Controllers\Group\GroupController',  ['parameters' => [
    'group' => 'id'
]])->except([
    'create', 'edit'
]);
Route::get('user-group', '\App\Http\Controllers\Group\UserGroupsController@index');


Route::post('group/{group_id}/user/{id}', '\App\Http\Controllers\Group\GroupUsersController@update');
Route::resource('group/{group_id}/user', '\App\Http\Controllers\Group\GroupUsersController', ['parameters' => [
    'user' => 'id'
]]) ->except([
    'create', 'edit'
]);

//UnverifiedGroupUsersController
Route::post('unverified-group-user/{id}', '\App\Http\Controllers\Group\UnverifiedGroupUsersController@update');
Route::resource('unverified-group-user', '\App\Http\Controllers\Group\UnverifiedGroupUsersController')->except([
    'create', 'edit', 'store'
]);


Route::post('season/{id}', '\App\Http\Controllers\Season\SeasonController@update');
Route::resource('season', '\App\Http\Controllers\Season\SeasonController',  ['parameters' => [
    'season' => 'id'
]])->except([
    'create', 'edit'
]);
Route::get('active_seasons', '\App\Http\Controllers\Season\ActiveSeasonController@index');

Route::post('season/{id}/generator', '\App\Http\Controllers\Season\SeasonGeneratorController@update');
Route::resource('season/{id}/generator', '\App\Http\Controllers\Season\SeasonGeneratorController',  ['parameters' => [
   'season' => 'id'
]])->except([
    'create', 'edit', 'show'
]);
Route::get('season/{id}/generator/play_dates', '\App\Http\Controllers\Season\SeasonGeneratorController@playDates');
Route::get('season/{id}/generator/new', '\App\Http\Controllers\Season\SeasonGeneratorController@generateSeason');

Route::post('season/{season_id}/absence/{id}', '\App\Http\Controllers\Season\AbsenceController@update');
Route::resource('season/{season_id}/absence', '\App\Http\Controllers\Season\AbsenceController',  ['parameters' => [
    'absence' => 'id'
]])->except([
    'create', 'edit', 'show'
]);


//delete method doesn't work on 000webhost

Route::post('profile/delete', '\App\Http\Controllers\User\ProfileController@destroy');
Route::post('user/{id}/delete', '\App\Http\Controllers\User\UserController@destroy');
Route::post('group/{id}/delete', '\App\Http\Controllers\Group\GroupController@destroy');
Route::post('group/{group_id}/user/{id}/delete', '\App\Http\Controllers\Group\GroupUsersController@destroy');
Route::post('season/{id}/delete', '\App\Http\Controllers\Season\SeasonController@destroy');
Route::post('season/{id}/generator/delete', '\App\Http\Controllers\Season\SeasonGeneratorController@destroy');
Route::post('absence/{id}/delete', '\App\Http\Controllers\Season\AbsenceController@destroy');