<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->middleware('auth:api');
        $this->middleware('season')->except('index', 'playDates', 'seasonUsers', 'seasonAbsences');
        $this->season = $seasonRepo;
        $this->absence = $absenceRepo;
        $this->team = $teamRepo;
    }
    
    /**
     * Displays all seasons where the authenticated user is season admin or is a member of the group of users connected to the season
     *
     * @return \Illuminate\Http\Response
     */
    public function index($seasonId)
    {
        $season = $this->season->getSeason($seasonId);
        $seasonGenerator = GeneratorFactory::generate($season->type);
        return response()->json($seasonGenerator->getSeasonCalendar($season), 200);
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json("to be made store", 200);

        /*
        $this->seasonValidator->validateCreateSeason($request);
        $userId = auth()->user()->id;
        $season = $this->season->create($request->all(), $userId);
        return response()->json($season, 200);
        */
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
        return response()->json("to be made update", 200);
        /*
        $this->seasonValidator->validateCreateSeason($request);
        $season = $this->season->update($request->all(), $id);
        return response()->json($season, 200);
        */
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
        $daysSeason = $seasonGenerator->getVuePlayDates($season->begin, $season->end);
        return response()->json($daysSeason, 200);
    }

    public function seasonUsers($seasonId)
    {
        return response()->json($this->team->getSeasonUsers($seasonId), 200);
    }

    public function seasonAbsences($seasonId)
    {
        return response()->json($this->absence->getSeasonAbsenceArray($seasonId), 200);
    }




}
