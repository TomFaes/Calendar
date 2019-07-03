<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IGroup;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IAbsence;

use App\Validators\SeasonValidation;

class SeasonsController extends Controller
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

    //new controller -> create
    public function generateSeason($seasonId){
        $season = $this->season->getSeason($seasonId);
        $listUsers = $this->group->getArrayOfGroupUsers($season->group_id);
        
        //build controle in to check if no other teams have been created
        $seasonGenerator = \App\Services\GenerateSeason\GenerateSeasonFactory::generate($season->type);
        $seasonJson = $seasonGenerator->generateSeason($seasonId);
        $seasonArray = $seasonGenerator->convertJsonToArraySeason($seasonJson);
        
        $days = $this->season->get7DaySeasonDates($seasonId);
        $seasonAbsences = $this->absence->getSeasonAbsenceArray($seasonId);
        $seasonUsers = $this->user->getUsersFromList($listUsers);
        
        return view('season.generateSeason')->with('days', $days)->with('season', $season)->with('seasonAbsences', $seasonAbsences)->with('seasonArray', $seasonArray)->with('seasonUsers', $seasonUsers)->with('seasonJson', $seasonJson);
    }
    // new controller save
    public function saveSeason(Request $request, $seasonId){
        
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = \App\Services\GenerateSeason\GenerateSeasonFactory::generate($season->type);

        $request->input('jsonSeason') != "" ? $json = $request->input('jsonSeason') : "";
        $seasonGenerator->saveSeason($json);

        return redirect()->to('season/')->send();
    }
    
    //new controller -> move to generator
    public function nextGame(){
        $playData = array();
        $seasons = $this->season->getSeasonsFromList($this->team->getArrayOfSeasons(Auth::user()->id));
        foreach($seasons as $season){

            $seasonGenerator = \App\Services\GenerateSeason\GenerateSeasonFactory::generate($season->type);
            
            
            
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
    
    
}
