<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\ITeam;

use App\Services\SeasonGeneratorService\GeneratorFactory;

//change name to GeneratedSeasonController???
class SeasonGeneratorController extends Controller
{
    protected $seasonRepo;
    protected $teamRepo;
    
    public function __construct(ISeason $seasonRepo, Iteam $teamRepo)
    {
        $this->middleware('season')->except('index', 'public', 'playDates');
        $this->seasonRepo = $seasonRepo;
        $this->teamRepo = $teamRepo;
    }
    
    /**
     * Get the season
     *
     * @return \Illuminate\Http\Response
     */
    public function index($seasonId)
    {
        $season = $this->seasonRepo->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        return response()->json($seasonGenerator->getSeasonCalendar($season), 200);
    }

    public function public($seasonId){
        $season = $this->seasonRepo->getSeason($seasonId);       
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
        $season = $this->seasonRepo->getSeason($seasonId);
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $season = $this->seasonRepo->getSeason($id);
        $seasonGenerator = GeneratorFactory::generate($season->type);

        $seasonGenerator->savePrefilledSeason($request['teamRange']);
        $this->seasonRepo->seasonIsGenerated($id);
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
        $season = $this->seasonRepo->checkIfSeasonIsStarted($id);
        if(count($season) == 0){
            return response()->json(false, 200);
        }
        $this->teamRepo->deleteTeamsFromSeason($id);
        $this->seasonRepo->seasonIsNotGenerated($id);
        return response()->json('Calendar is deleted', 200); 
    }

    public function playDates($seasonId)
    {
        $season = $this->seasonRepo->getseason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $daysSeason['data'] = $seasonGenerator->getPlayDates($season->begin, $season->end);
        return response()->json($daysSeason, 200);
    }

    public function generateSeason($seasonId)
    {
        $season = $this->seasonRepo->getSeason($seasonId);
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
        $season = $this->seasonRepo->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $calendar = $seasonGenerator->generateEmptySeason($season);
        return response()->json($calendar, 200);
    }
}
