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

class SeasonController extends Controller
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
        $this->seasonValidator = $seasonValidation;
        $this->user = $userRepo;
        $this->group = $groupRepo;
        $this->team = $teamRepo;
        $this->absence = $absenceRepo;
        
        $this->middleware('season:delete', ['only' => ['destroy']]);
        $this->middleware('season:', ['only' => ['edit', 'update']]);
        $this->middleware('admin:Editor', ['only' => ['create', 'store', 'edit', 'update']]);
    }
    
    /**
     * Displays all seasons where the authenticated user is season admin or is a member of the group of users connected to the season
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $seasonteamArray = $this->team->getArrayOfSeasons(Auth::user()->id);
        $seasonAdminArray = $this->season->getArrayOfAdminSeasons(Auth::user()->id);
        //get list of groep seasons
        $groupsArray = $this->group->getArrayOfUserGroups(Auth::user()->id);
        $groupArray = $this->season->getArrayOfGroupSeasons($groupsArray);
        
        //merge all season id into 1 array
        $seasonIdArray = array_merge($seasonteamArray, $seasonAdminArray);
        $seasonIdArray = array_merge($seasonIdArray, $groupArray);
        
        $seasons = $this->season->getSeasonsFromList($seasonIdArray);
        return view('season.index')->with('seasons', $seasons);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $users = $this->user->getAllUsers();
        $groups = $this->group->getAllGroups();
        return view('season.create')->with('users', $users)->with('groups', $groups);
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->seasonValidator->validateCreateSeason($request);
        $this->season->create($request);
        return redirect()->to('season/')->send();
    }
    
    /**
     * Display the specified resource. This method will return the fully generated kalender if there is a season
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $list = $this->team->getArrayOfSeasonUsers($id);
        $season = $this->season->getSeason($id);
        $seasonAbsences = $this->absence->getSeasonAbsenceArray($id);

        $seasonGenerator = GeneratorFactory::generate($season->type);
        $days = $seasonGenerator->getPlayDates($season->begin, $season->end);

        $seasonJson = $seasonGenerator->getSeasonCalendar($season);

        $seasonUsers = $this->user->getUsersFromList($list);

        return view('season.show')->with('days', $days)->with('season', $season)->with('seasonAbsences', $seasonAbsences)->with('seasonUsers', $seasonUsers)->with('seasonJson', $seasonJson);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $season = $this->season->getSeason($id);
        $users = $this->user->getAllUsers();
        $groups = $this->group->getAllGroups();
        return view('season.edit')->with('season', $season)->with('users', $users)->with('groups', $groups);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $this->seasonValidator->validateCreateSeason($request);
        $this->season->update($request, $id);
        return redirect()->to('season/')->send();
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $this->absence->deleteSeasonAbsence($id);
        $this->season->delete($id);
        return redirect()->to('season/')->send();
    }    
}
