<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\ISeason;

use App\Validators\TeamValidation;

class TeamController extends Controller
{
    /** @var App\Repositories\Contracts\ISeason */
    protected $season;
    /** @var App\Repositories\Contracts\ITeam */
    protected $team;

    protected $teamValidator;

    public function __construct(ITeam $teamRepo, TeamValidation $teamValidation, ISeason $seasonRepo) {
        $this->middleware('auth');

        $this->team = $teamRepo;
        $this->season = $seasonRepo;

        $this->teamValidator = $teamValidation;
    }

    protected function checkUser($seasonId){
        $season = $this->season->getSeason($seasonId);
        if(Auth::user()->id == $season->admin->id){
            return true;
        }
        return false;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isset($request->seasonId) === true){
            $seasonId = $request->seasonId;
        }else{
            return redirect('season')->with('error', 'Unknown season id');
        }

        if($this->checkUser($seasonId) === true){
            $this->teamValidator->validateCreateTeam($request);
            $this->team->create($request, $seasonId);
            return redirect()->to('season/'.$seasonId)->send();
        }
        return redirect('season')->with('error', 'Je hebt geen rechten om teams aan dit seizoen toe te voegen');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($seasonId, $teamId)
    {

        if($this->checkUser($seasonId) === true){
            $seasonId = $this->team->deleteTeam($teamId);
            return redirect()->to('season/'.$seasonId)->send();
        }
        return redirect('season')->with('error', 'Je hebt geen rechten om teams van dit seizoen te verwijderen');
    }
}