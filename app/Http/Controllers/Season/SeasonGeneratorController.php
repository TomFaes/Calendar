<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IAbsence;

use App\Validators\SeasonValidation;

class SeasonGeneratorController extends Controller
{
    /** @var App\Repositories\Contracts\ISeason */
    protected $season;
    /** @var App\Repositories\Contracts\IUser */
    protected $user;
    /** @var App\Repositories\Contracts\IGroup */
    protected $group;
    /** @var App\Repositories\Contracts\ITeam */
    protected $team;
    /** @var App\Repositories\Contracts\IAbsence */
    protected $absence;
    
    /** @var App\Validators\SeasonValidation */
    protected $seasonValidator;
    
    public function __construct(ISeason $seasonRepo, SeasonValidation $seasonValidation, IUser $userRepo, IGroup $groupRepo, ITeam $teamRepo, IAbsence $absenceRepo){
        $this->middleware('auth');
        $this->season = $seasonRepo;
        $this->user = $userRepo;
        $this->team = $teamRepo;
        $this->group = $groupRepo;
        $this->absence = $absenceRepo;
        
        $this->middleware('admin:Editor', ['only' => ['edit', 'update']]);
    }
    /**
     * Displays all the current season Calendars where you are active
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $playData = array();
        $seasons = $this->season->getSeasonsFromList($this->team->getArrayOfSeasons(Auth::user()->id));

        foreach($seasons as $season){
            //remove if the old generator is removed
            if($season->type == "GenerateThursdaySeason"){
                continue;
            }
            $seasonGenerator = \App\Services\SeasonGeneratorService\GeneratorFactory::generate($season->type);
            $playData['teams'][$season->id] = $seasonGenerator->getNextPlayDay($season->id, \Carbon\Carbon::parse($season->begin)->format('l'), $season->start_hour);
            
            if(count($playData['teams'][$season->id]) == 0) {
                if($season->begin > \Carbon\Carbon::now()->format("Y-m-d")){
                    $playData['teams'][$season->id] = $this->team->getTeamsOnDate($season->id, $season->begin);;
                }
            }

            if(count($playData['teams'][$season->id]) > 0) {
                $list = $this->team->getArrayOfSeasonUsers($season->id);

                $playData['date'][$season->id] = $playData['teams'][$season->id][0]['date'];
                $playData['season'][$season->id] = $this->season->getSeason($season->id)->toArray();
                $playData['users'][$season->id] = $this->user->getUsersFromList($list)->toArray();

                foreach($playData['teams'][$season->id] as $teams){
                    $playData['dayplayers'][$season->id][$teams->player_id] = substr($teams->team, -1);
                }

                foreach($this->absence->getSeasonAbsence($season->id)->toArray() as $absence){
                    $playData['dayabsence'][$season->id][$absence['user_id']][$absence['date']] = $absence['date'];
                }
            }else{
                unset($playData['teams'][$season->id]);
            }
        }
        return view('season.nextgame')->with('playData', $playData);
    }

    /**
     * Shows the season that is generated and where you have the options to regenerate or save the displayed season
     *
     * @param  int  $seasonid
     * @return \Illuminate\Http\Response
     */
    public function edit($seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $listUsers = $this->group->getArrayOfGroupUsers($season->group_id);

        $seasonGenerator = \App\Services\SeasonGeneratorService\GeneratorFactory::generate($season->type);
        $seasonJson = $seasonGenerator->generateSeason($seasonId);

        $days = $seasonGenerator->getPlayDates($season->begin, $season->end);
        $seasonAbsences = $this->absence->getSeasonAbsenceArray($seasonId);
        $seasonUsers = $this->user->getUsersFromList($listUsers);

        return view('season.generateSeason')->with('days', $days)->with('season', $season)->with('seasonAbsences', $seasonAbsences)->with('seasonUsers', $seasonUsers)->with('seasonJson', $seasonJson);
    }

    /**
     * Save the generated season
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $seasonId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = \App\Services\SeasonGeneratorService\GeneratorFactory::generate($season->type);
        
        $request->input('jsonSeason') != "" ? $json = $request->input('jsonSeason') : "";
        $seasonGenerator->saveSeason($json);

        return redirect()->to('season/')->send();
    }
}
