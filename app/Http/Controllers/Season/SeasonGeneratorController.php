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
        //$this->middleware('auth:api')->except('index');
        $this->middleware('season')->except('index', 'public', 'playDates', 'seasonUsers', 'seasonAbsences');
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
        //if($season->public !== 1 && Auth::guard('api')->check() === false){
        /*
        if($season->public !== 1){
             return response()->json("This is not a public season", 203);
        }
        */
        $seasonGenerator = GeneratorFactory::generate($season->type);
        return response()->json($seasonGenerator->getSeasonCalendar($season), 200);
    }

    public function public($seasonId){
        $season = $this->season->getSeason($seasonId);       
        if($season->public == 0){
            return response()->json($season->name." is not a public season", 203);
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
        $season = $this->season->getSeason($id);
        $seasonGenerator = GeneratorFactory::generate($season->type);

        $seasonGenerator->savePrefilledSeason($request['teamRange']);
        $this->season->seasonIsGenerated($id);
        return response()->json("season is updated", 200);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $season = $this->season->checkIfSeasonIsStarted($id);
        if(count($season) == 0){
            return response()->json(false, 200);
        }
        $this->team->deleteTeamsFromSeason($id);
        $this->season->seasonIsNotGenerated($id);
        return response()->json('Calendar is deleted', 200); 
    }

    public function playDates($seasonId)
    {
        $season = $this->season->getseason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $daysSeason['data'] = $seasonGenerator->getPlayDates($season->begin, $season->end);
        return response()->json($daysSeason, 200);
    }

    public function generateSeason($seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $calendar = $seasonGenerator->generateSeason($season);
        return response()->json($calendar, 200);

        /*
        //only for testing
        //used for getting a season with equeal total & team3 stats
        for($x=0; $x<20;$x++){ 
            $calendar = $seasonGenerator->generateSeason($season);
            $testStats = 0;
            $testTeam3 = 0;
            $test = true;
            foreach($calendar['stats'] AS $key => $stats){
                if($key == ""){
                    continue;
                }

                if($testStats != $stats['total'] && $testStats > 0){
                    $test = false;
                }
                if($testTeam3 != $stats['team3'] && $testTeam3 > 0){
                    $test = false;
                }
                if($test == false){
                    break;
                }
                $testStats = $stats['total'];
                $testTeam3 = $stats['team3'];
            }
            if($test == true){
                break;
            }
        }
        return response()->json($calendar, 200);
        */
    }

    public function createEmptySeason($seasonId){
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $calendar = $seasonGenerator->generateEmptySeason($season);
        return response()->json($calendar, 200);
    }
}
