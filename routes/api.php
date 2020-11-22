<?php

use Illuminate\Http\Request;

use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\UserGroupsController;
use App\Http\Controllers\Group\GroupUsersController;
use App\Http\Controllers\Group\UnverifiedGroupUsersController;
use App\Http\Controllers\Season\SeasonController;
use App\Http\Controllers\Season\ActiveSeasonController;
use App\Http\Controllers\Season\SeasonGeneratorController;
use App\Http\Controllers\Season\AbsenceController;
use App\Http\Controllers\Season\TeamController;

//All routes for profiles
Route::get('/profile', [ProfileController::class, 'index']);
Route::post('/profile', [ProfileController::class, 'update']);

//All routes for users
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::post('/user/{id}', [UserController::class, 'update']);

//All routes for groups
Route::get('/group', [GroupController::class, 'index']);
Route::post('/group', [GroupController::class, 'store']);
Route::post('/group/{id}', [GroupController::class, 'update']);

Route::get('/user-group', [UserGroupsController::class, 'index']);

Route::get('group/{group_id}/user', [GroupUsersController::class, 'index']);
Route::post('/group/{group_id}/user', [GroupUsersController::class, 'store']);
Route::post('/group/{group_id}/user/{id}', [GroupUsersController::class, 'update']);

//All routes for unverified users
Route::get('/unverified-group-user', [UnverifiedGroupUsersController::class, 'index']);
//Route::post('/unverified-group-user', [UnverifiedGroupUsersController::class, 'store']);
Route::post('/unverified-group-user/{id}', [UnverifiedGroupUsersController::class, 'update']);

//All routes for Seasons
Route::get('/season', [SeasonController::class, 'index']);
Route::post('/season', [SeasonController::class, 'store']);
Route::post('/season/{id}', [SeasonController::class, 'update']);

Route::get('/active_seasons', [ActiveSeasonController::class, 'index']);

Route::get('/season/{id}/generator', [SeasonGeneratorController::class, 'index']);
Route::post('/season/{id}/generator', [SeasonGeneratorController::class, 'store']);
Route::post('/season/{id}/generator/{generator_id}', [SeasonGeneratorController::class, 'update']);
Route::get('/season/{id}/generator/play_dates', [SeasonGeneratorController::class, 'playDates']);
Route::get('/season/{id}/generator/new', [SeasonGeneratorController::class, 'generateSeason']);

Route::get('season/{season_id}/absence/', [AbsenceController::class, 'index']);
Route::post('season/{season_id}/absence', [AbsenceController::class, 'store']);

//All routes for teams
Route::post('team/range', [TeamController::class, 'updateRange']);

//delete method doesn't work on 000webhost
Route::post('/profile/delete', [ProfileController::class, 'destroy']);
Route::post('/user/{id}/delete', [UserController::class, 'destroy']);
Route::post('/group/{id}/delete', [GroupController::class, 'destroy']);
Route::post('group/{group_id}/user/{id}/delete', [GroupUsersController::class, 'destroy']);
Route::post('/unverified-group-user/{id}/delete', [UnverifiedGroupUsersController::class, 'destroy']);
Route::post('/season/{id}/delete', [SeasonController::class, 'destroy']);

Route::post('/season/{id}/generator/{season_id}/delete', [SeasonGeneratorController::class, 'destroy']);
Route::post('/absence/{id}/delete', [AbsenceController::class, 'destroy']);
