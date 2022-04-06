<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\UserGroupsController;
use App\Http\Controllers\Group\GroupUsersController;
use App\Http\Controllers\Season\SeasonController;
use App\Http\Controllers\Season\ActiveSeasonController;
use App\Http\Controllers\Season\SeasonGeneratorController;
use App\Http\Controllers\Season\AbsenceController;
use App\Http\Controllers\Season\TeamController;

Route::post('login', [AuthenticationController::class, 'login']);
Route::get('/logout', [AuthenticationController::class, 'logout']);
//Route::post('register', [AuthController::class, 'register']);

Route::get('/season/{id}/public', [SeasonGeneratorController::class, 'public']);

Route::middleware('auth:sanctum')->group( function () {
    //All routes for profiles
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('update-password', [ProfileController::class, 'updatePassword']);

    //All routes for groups
    //Route::get('/group', [GroupController::class, 'index']);
    Route::post('/group', [GroupController::class, 'store']);
    Route::get('group/{id}', [GroupController::class, 'show']);
    Route::post('/group/{id}', [GroupController::class, 'update']);

    Route::get('/user-group', [UserGroupsController::class, 'index']);

    Route::get('group/{group_id}/users', [GroupUsersController::class, 'index']);
    Route::post('/group/{group_id}/user', [GroupUsersController::class, 'store']);
    Route::post('/group/{group_id}/user/{id}', [GroupUsersController::class, 'update']);

    Route::post('group/{group_id}/user/{id}/regenerate_code', [GroupUsersController::class, 'regenerateGroupUserCode']);
    Route::post('join_group', [GroupUsersController::class, 'joinGroup']);

    //All routes for Seasons
    Route::get('/season', [SeasonController::class, 'index']);
    Route::get('/season/{id}', [SeasonController::class, 'show']);
    Route::post('/season', [SeasonController::class, 'store']);
    Route::post('/season/{id}', [SeasonController::class, 'update']);
    Route::get('/season/{id}/is_generated', [SeasonController::class, 'seasonIsGenerated']);

    Route::get('/active_seasons', [ActiveSeasonController::class, 'index']);

    Route::get('season/{season_id}/absence', [AbsenceController::class, 'index']);
    Route::post('season/{season_id}/absence', [AbsenceController::class, 'store']);

    Route::get('/season/{id}/generator', [SeasonGeneratorController::class, 'index']);
    Route::get('/season/{id}/generator/new', [SeasonGeneratorController::class, 'generateSeason']);
    Route::post('/season/{id}/generator', [SeasonGeneratorController::class, 'store']);
    Route::post('/season/{id}/generator/{season_id}', [SeasonGeneratorController::class, 'update']);
    Route::get('/season/{id}/generator/play_dates', [SeasonGeneratorController::class, 'playDates']);
    Route::get('/season/{id}/generator/create_empty_season', [SeasonGeneratorController::class, 'createEmptySeason']);

    Route::post('team/range', [TeamController::class, 'updateRange']);
    Route::post('team/{id}/ask_for_replacement', [TeamController::class, 'askForReplacement']);
    Route::post('team/{id}/cancel_request_for_replacement', [TeamController::class, 'cancelRequestForReplacement']);
    Route::post('team/{id}/confirm_replacement', [TeamController::class, 'confirmReplacement']);

    //delete method doesn't work on 000webhost
    Route::post('/profile/delete', [ProfileController::class, 'destroy']);
    Route::post('/group/{id}/delete', [GroupController::class, 'destroy']);
    Route::post('group/{group_id}/user/{id}/delete', [GroupUsersController::class, 'destroy']);
    Route::post('/season/{id}/delete', [SeasonController::class, 'destroy']);
    Route::post('/season/{id}/generator/{season_id}/delete', [SeasonGeneratorController::class, 'destroy']);
    Route::post('/absence/{id}/delete', [AbsenceController::class, 'destroy']);
});
