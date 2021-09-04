<?php

namespace App\Http\Controllers\Season;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\Contracts\ISeason;
use App\Repositories\Contracts\IAbsence;

use App\Validators\SeasonValidation;

class SeasonController extends Controller
{
    /** @var App\Repositories\Contracts\ISeason */
   protected $season;

    /** @var App\Repositories\Contracts\IAbsence */
    protected $absence;
    
    /** @var App\Validators\SeasonValidation */
    protected $seasonValidator;
    
    public function __construct(ISeason $seasonRepo, SeasonValidation $seasonValidation,  IAbsence $absenceRepo)
    {
        $this->middleware('auth:api');
        $this->middleware('season')->except('store', 'index', 'show');
        $this->season = $seasonRepo;
        $this->seasonValidator = $seasonValidation;
        $this->absence = $absenceRepo;
    }
    
    /**
     * Displays all seasons where the authenticated user is season admin or is a member of the group of users connected to the season
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->season->getSeasonsOfUser(Auth::user()->id), 200);
    }
    
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->seasonValidator->validateCreateSeason($request);
        $userId = auth()->user()->id;
        $season = $this->season->create($request->all(), $userId);
        return response()->json($season, 200);
    }

     /**
     * show a resource in storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->season->getSeason($id), 200);
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
        $this->seasonValidator->validateCreateSeason($request);
        $season = $this->season->update($request->all(), $id);
        return response()->json($season, 200);
    }

    public function seasonIsGenerated($seasonId){
        $season = $this->season->seasonIsGenerated($seasonId);
        return response()->json($season, 200);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->absence->deleteSeasonAbsence($id);
        $this->season->delete($id);
        return response()->json("Season is deleted", 204);
    }
}
