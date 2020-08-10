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
use App\Services\SeasonGeneratorService\GeneratorFactory;

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
        
        //$this->middleware('admin:Editor', ['only' => ['edit', 'update']]);
        $this->middleware('season:Generate', ['only' => ['create', 'store']]);
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
            $seasonGenerator = GeneratorFactory::generate($season->type);
            //Get all data: it should contain date, season, users, display, absenceDays
            $playData[$season->id] = $seasonGenerator->getNextPlayDay($season);
            if(isset($playData[$season->id]['date']) === false){
                unset($playData[$season->id]);
            }
        }
        return view('season.nextgame')->with('seasonsdata', $playData);
    }

    /**
     * create the season that is generated and where you have the options to regenerate or save the displayed season
     *
     * @param  int  $seasonid
     * @return \Illuminate\Http\Response
     */
    public function create($seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $listUsers = $this->group->getArrayOfGroupUsers($season->group_id);

        $seasonGenerator = GeneratorFactory::generate($season->type);
        $seasonJson = $seasonGenerator->generateSeason($season);

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
    public function store(Request $request, $seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        
        $request->input('jsonSeason') != "" ? $json = $request->input('jsonSeason') : "";
        $seasonGenerator->saveSeason($json);

        return redirect()->to('season/')->send();
    }
}
