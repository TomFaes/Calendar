<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Repositories\Contracts\ITeam;


use App\Validators\TeamValidation;

class TeamsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Team Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling of teams
    |
    */
    protected $team;
    protected $teamValidator;

    public function __construct(ITeam $teamRepo, TeamValidation $teamValidation) {
        $this->middleware('auth');
        $this->team = $teamRepo;
        $this->teamValidator = $teamValidation;
    }
    
    public function addTeam(Request $request, $seasonId){
        $this->teamValidator->validateCreateTeam($request);
        $this->team->create($request, $seasonId);
        return redirect()->to('season/'.$seasonId)->send();
    }

    public function destroy($teamId){
        $seasonId = $this->team->deleteTeam($teamId);
        return redirect()->to('season/'.$seasonId)->send();
    }
}
