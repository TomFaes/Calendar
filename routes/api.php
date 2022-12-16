<?php

use Illuminate\Support\Facades\Route;

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

use App\Http\Middleware\Security\Group;
use App\Http\Middleware\Security\GroupUser;
use App\Http\Middleware\Security\SeasonAbsence;
use App\Http\Middleware\Security\SeasonAdmin;
use App\Http\Middleware\Security\SeasonUser;

//Info: //delete method doesn't work on 000webhost

Route::post('login', [AuthenticationController::class, 'login']);
Route::get('/logout', [AuthenticationController::class, 'logout']);
//Route::post('register', [AuthController::class, 'register']);

Route::get('/season/{season}/public', [SeasonGeneratorController::class, 'public']);

Route::middleware('auth:sanctum')->group( function () {
    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->group( function (){
            Route::get('/', 'index');
            Route::post('/', 'update');
            Route::post('/delete', 'destroy');
        });

    //All routes for groups
    Route::post('/group', [GroupController::class, 'store']);
    Route::prefix('group/{group}')
        ->group(function (){
            Route::controller(GroupController::class)
                ->middleware(Group::class)
                ->group(function(){
                    Route::get('/', 'show');
                    Route::post('/', 'update');
                    Route::post('/delete', 'destroy');
                });

            Route::controller(GroupUsersController::class)
                ->middleware(GroupUser::class)
                ->group(function(){
                    Route::get('/users', 'index');
                    Route::post('/user', 'store');
                    Route::post('/user/{group_user}', 'update');
                    Route::post('/user/{group_user}/delete', 'destroy');
                    Route::post('/user/{group_user}/regenerate_code', 'regenerateGroupUserCode');
                });
        });

    Route::get('/user-group', [UserGroupsController::class, 'index']);
    Route::post('join_group', [GroupUsersController::class, 'joinGroup']);

     //All routes for Seasons
    Route::prefix('season/{season}')->group(function (){
        Route::controller(SeasonController::class)
            ->middleware(SeasonAdmin::class)
            ->group(function(){
                Route::post('/', 'update');
                Route::get('/is_generated', 'seasonIsGenerated');
                Route::post('/delete', 'destroy');
            });

        Route::controller(SeasonController::class)
            ->middleware(SeasonAdmin::class)
            ->prefix('/generator')
            ->group(function(){
                Route::get('/new', [SeasonGeneratorController::class, 'generateSeason']);
                Route::post('/', [SeasonGeneratorController::class, 'store']);
                Route::post('/{season_id}', [SeasonGeneratorController::class, 'update']);
                Route::get('/create_empty_season', [SeasonGeneratorController::class, 'createEmptySeason']);
                Route::post('/{season_id}/delete', [SeasonGeneratorController::class, 'destroy']);
            });
        Route::get('/absence/sent_mail_register_absence', [AbsenceController::class, 'sentMailRegisterAbsence'])->middleware(SeasonAdmin::class);
        Route::post('/team/range', [TeamController::class, 'updateRange'])->middleware(SeasonAdmin::class);

        Route::middleware(SeasonUser::class)
            ->group(function(){
                Route::get('/', [SeasonController::class, 'show']);
                Route::get('/generator', [SeasonGeneratorController::class, 'index']);
                Route::get('/generator/play_dates', [SeasonGeneratorController::class, 'playDates']);
            });

        Route::middleware(SeasonAbsence::class)
            ->group(function(){
                Route::controller(AbsenceController::class)
                    ->group(function(){
                        Route::get('/absence', 'index');
                        Route::post('/absence', 'store');
                        Route::post('/absence/{absence}/delete', 'destroy');
                    });
            });
            
        Route::controller(TeamController::class)
            ->prefix('/team/{team}')
            ->group(function(){ 
                Route::post('/ask_for_replacement', 'askForReplacement');
                Route::post('/cancel_request_for_replacement', 'cancelRequestForReplacement');
                Route::post('/confirm_replacement', 'confirmReplacement');
            });
        
    });

    Route::get('/season', [SeasonController::class, 'index']);
    Route::post('/season', [SeasonController::class, 'store']);
    Route::get('/active_seasons', ActiveSeasonController::class);


    

    
});
