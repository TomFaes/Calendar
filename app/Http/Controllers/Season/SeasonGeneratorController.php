<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IAbsence;
use App\Repositories\Contracts\ITeam;

use App\Services\SeasonGeneratorService\GeneratorFactory;

class SeasonGeneratorController extends Controller
{
    /** @var App\Repositories\Contracts\ISeason */
   protected $season;

    /** @var App\Repositories\Contracts\IAbsence */
    protected $absence;

    /** @var App\Repositories\Contracts\IAbsence */
    protected $team;
    
    public function __construct(ISeason $seasonRepo, IAbsence $absenceRepo, Iteam $teamRepo)
    {
        $this->middleware('auth:api')->except('index');
        $this->middleware('season')->except('index', 'playDates', 'seasonUsers', 'seasonAbsences');
        $this->season = $seasonRepo;
        $this->absence = $absenceRepo;
        $this->team = $teamRepo;
    }
    
    /**
     * Get the season
     *
     * @return \Illuminate\Http\Response
     */
    public function index($seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        if($season->public !== 1 && Auth::guard('api')->check() === false){
             return response()->json("This is not a public season", 203);
        }
        $seasonGenerator = GeneratorFactory::generate($season->type);
        return response()->json($seasonGenerator->getSeasonCalendar($season), 200);
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);

        if ($request->input('jsonSeason') != "") {
            $json = $request->input('jsonSeason');
            $seasonGenerator->saveSeason($json);
            return response()->json("season is made", 200);
        }
        return response()->json("season couldn't be generated", 200);
    }
    
    /**
     * Update the specified resource in storage.
     * 
     * !!!! As long as not all generators are update to use this function keep the $request[updateRange] active.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $updateVersion = $request['updateRange'] ?? "";
        if($updateVersion != "pregeneratedseason"){
            return response()->json("season couldn't be generated", 200);
        }

        $season = $this->season->getSeason($id);
        $seasonGenerator = GeneratorFactory::generate($season->type);

        if($updateVersion == 'pregeneratedseason'){
            $seasonGenerator->savePrefilledSeason($request['teamRange']);
            $this->season->seasonIsGenerated($id);
            return response()->json("season is made", 200);
        }
        return response()->json("season couldn't be generated", 200);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json("to be made destroy", 200);
        /*
        $this->absence->deleteSeasonAbsence($id);
        $this->season->delete($id);
        return response()->json("Season is deleted", 204);
        */
    }

    public function playDates($seasonId)
    {
        $season = $this->season->getseason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $daysSeason = $seasonGenerator->getPlayDates($season->begin, $season->end);
        return response()->json($daysSeason, 200);
    }

    public function generateSeason($seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);

        $calendar = $seasonGenerator->generateSeason($season);
        return response()->json($calendar, 200);
    }

    public function createEmptySeason($seasonId, Request $request){
        if($request->teams == 0){
            return response()->json("Teams must be bigger then 0 to create an empty season", 412);
        }
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $calendar = $seasonGenerator->generateEmptySeason($season, $request->teams);
        return response()->json($calendar, 200);
    }
}
