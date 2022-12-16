<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Http\Request;

use App\Services\SeasonGeneratorService\GeneratorFactory;
use App\Services\SeasonService;

//change name to GeneratedSeasonController???
class SeasonGeneratorController extends Controller
{
    protected $seasonService;
    
    public function __construct(SeasonService $seasonService)
    {
        $this->seasonService = $seasonService;
    }
    
    public function index(Season $season)
    {
        $seasonGenerator = GeneratorFactory::generate($season->type);
        return response()->json($seasonGenerator->getSeasonCalendar($season), 200);
    }

    public function public(Season $season){     
        if($season->public == 0){
            return response()->json($season->name." is not a public season", 203);
        } 
        $seasonGenerator = GeneratorFactory::generate($season->type);
        return response()->json($seasonGenerator->getSeasonCalendar($season), 200);
    }
    
    public function store(Request $request, Season $season)
    {
        $seasonGenerator = GeneratorFactory::generate($season->type);

        if ($request->input('jsonSeason') != "") {
            $json = $request->input('jsonSeason');
            $seasonGenerator->saveSeason($json);
            return response()->json("season is made", 200);
        }
        return response()->json("season couldn't be generated", 200);
    }
    
    public function update(Request $request, Season $season)
    {
        $seasonGenerator = GeneratorFactory::generate($season->type);

        $seasonGenerator->savePrefilledSeason($request['teamRange']);
        $season->is_generated = 1;
        $season->save();
        return response()->json("season is updated", 200);
    }
    
    public function destroy(Season $season)
    {
        
        $seasonStarted = $this->seasonService->checkIfSeasonIsStarted($season);

        if($seasonStarted === true){
            return response()->json(false, 200);
        }
        
        Team::where('season_id', $season->id)->delete();
        $season->is_generated = 0;
        $season->save();
        return response()->json('Calendar is deleted', 200); 
    }

    public function playDates(Season $season)
    {
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $daysSeason['data'] = $seasonGenerator->getPlayDates($season->begin, $season->end);
        return response()->json($daysSeason, 200);
    }

    public function generateSeason(Season $season)
    {
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

    public function createEmptySeason(Season $season){
        $seasonGenerator = GeneratorFactory::generate($season->type);
        $calendar = $seasonGenerator->generateEmptySeason($season);
        return response()->json($calendar, 200);
    }
}
