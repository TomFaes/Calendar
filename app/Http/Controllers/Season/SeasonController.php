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
        $this->middleware('season:', ['only' => ['edit', 'update', 'generateSeason', 'saveSeason']]);
        $this->middleware('admin:Editor', ['only' => ['create', 'store', 'edit', 'update', 'generateSeason', 'saveSeason']]);
    }
    
    public function index(){
        $seasonteamArray = $this->team->getArrayOfSeasons(Auth::user()->id);
        $seasonAdminArray = $this->season->getArrayOfAdminSeasons(Auth::user()->id);
        //get list of groep seasons
        $groupsArray = $this->group->getArrayOfUserGroups(Auth::user()->id);
        $groupArray = $this->season->getArrayOfGroupSeasons($groupsArray);
        
        $seasonIdArray = array_merge($seasonteamArray, $seasonAdminArray);
        $seasonIdArray = array_merge($seasonIdArray, $groupArray);
        
        $seasons = $this->season->getSeasonsFromList($seasonIdArray, Auth::user()->role);
        return view('season.index')->with('seasons', $seasons);
    }
    
    public function create(){
        $users = $this->user->getAllUsers();
        $groups = $this->group->getAllGroups();
        return view('season.create')->with('users', $this->user->getAllUsers())->with('groups', $this->group->getAllGroups());
    }
    
    public function store(Request $request){
        $this->seasonValidator->validateCreateSeason($request);
        $this->season->create($request);
        return redirect()->to('season/')->send();
    }
    
    public function show($id){
        $list = $this->team->getArrayOfSeasonUsers($id);
        $season = $this->season->getSeason($id);
        $seasonGenerator = \App\Services\GenerateSeason\GenerateSeasonFactory::generate($season->type);
        
        $days = $this->season->get7DaySeasonDates($id);
        $seasonAbsences = $this->absence->getSeasonAbsenceArray($id);
        $seasonArray = $seasonGenerator->seasonRecap($id);
        $seasonUsers = $this->user->getUsersFromList($list);
        
        return view('season.show')->with('days', $days)->with('season', $season)->with('seasonAbsences', $seasonAbsences)->with('seasonArray', $seasonArray)->with('seasonUsers', $seasonUsers);
    }
    
    public function edit($id){
        $season = $this->season->getSeason($id);
        $users = $this->user->getAllUsers();
        $groups = $this->group->getAllGroups();
        return view('season.edit')->with('season', $season)->with('users', $users)->with('groups', $groups);
    }
    
    public function update(Request $request, $id){
        $this->seasonValidator->validateCreateSeason($request);
        $this->season->update($request, $id);
        return redirect()->to('season/')->send();
    }
    
    public function destroy($id){
        $this->absence->deleteSeasonAbsence($id);
        $season = $this->season->getSeason($id);
        $this->season->delete($id);
        return redirect()->to('season/')->send();
    }    
}
